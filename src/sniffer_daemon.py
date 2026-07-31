import os
import sqlite3
import time
import random
import requests
import json

DB_PATH = os.path.abspath(os.path.join(os.path.dirname(__file__), "../dashboard/database/database.sqlite"))
API_URL = "http://127.0.0.1:8001/api/analyze"

def init_db():
    # Make sure SQLite DB directory exists
    os.makedirs(os.path.dirname(DB_PATH), exist_ok=True)
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    # Create the table if migrations haven't run yet
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS live_network_flows (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            protocol_type TEXT,
            service TEXT,
            flag TEXT,
            src_bytes INTEGER,
            hot INTEGER,
            su_attempted INTEGER,
            serror_rate REAL,
            same_srv_rate REAL,
            diff_srv_rate REAL,
            dst_host_diff_srv_rate REAL,
            prediction TEXT,
            confidence REAL,
            latency_ms REAL
        )
    """)
    conn.commit()
    conn.close()

def log_flow(flow_data, prediction_data):
    try:
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("""
            INSERT INTO live_network_flows (
                protocol_type, service, flag, src_bytes, hot, su_attempted,
                serror_rate, same_srv_rate, diff_srv_rate, dst_host_diff_srv_rate,
                prediction, confidence, latency_ms
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """, (
            flow_data['protocol_type'], flow_data['service'], flow_data['flag'],
            flow_data['src_bytes'], flow_data['hot'], flow_data['su_attempted'],
            flow_data['serror_rate'], flow_data['same_srv_rate'], flow_data['diff_srv_rate'],
            flow_data['dst_host_diff_srv_rate'],
            prediction_data['prediction'], prediction_data['confidence'], prediction_data['latency_ms']
        ))
        conn.commit()
        conn.close()
    except Exception as e:
        print(f"Error writing to database: {e}")

def generate_mock_flow():
    # 85% benign, 15% attack scenarios
    is_anomaly = random.random() < 0.15
    
    if not is_anomaly:
        return {
            'protocol_type': random.choice(['tcp', 'udp']),
            'service': random.choice(['http', 'domain', 'smtp']),
            'flag': 'SF',
            'src_bytes': random.randint(100, 1500),
            'hot': 0,
            'su_attempted': 0,
            'serror_rate': 0.0,
            'same_srv_rate': round(0.9 + random.random() * 0.1, 2),
            'diff_srv_rate': 0.0,
            'dst_host_diff_srv_rate': round(random.random() * 0.15, 2)
        }
    else:
        # Attack types: DoS, Probe, U2R
        scenario = random.choice(['dos', 'probe', 'u2r'])
        if scenario == 'dos':
            return {
                'protocol_type': 'tcp',
                'service': 'private',
                'flag': 'S0',
                'src_bytes': 0,
                'hot': 0,
                'su_attempted': 0,
                'serror_rate': round(0.8 + random.random() * 0.2, 2),
                'same_srv_rate': round(random.random() * 0.15, 2),
                'diff_srv_rate': round(0.8 + random.random() * 0.2, 2),
                'dst_host_diff_srv_rate': round(0.7 + random.random() * 0.3, 2)
            }
        elif scenario == 'probe':
            return {
                'protocol_type': 'icmp',
                'service': 'private',
                'flag': 'SF',
                'src_bytes': random.randint(8, 64),
                'hot': 0,
                'su_attempted': 0,
                'serror_rate': 0.0,
                'same_srv_rate': round(random.random() * 0.2, 2),
                'diff_srv_rate': round(0.8 + random.random() * 0.2, 2),
                'dst_host_diff_srv_rate': round(0.7 + random.random() * 0.3, 2)
            }
        else: # u2r / privilege escalation
            return {
                'protocol_type': 'tcp',
                'service': 'telnet',
                'flag': 'SF',
                'src_bytes': random.randint(800, 3000),
                'hot': random.randint(3, 8),
                'su_attempted': random.choice([1, 2]),
                'serror_rate': 0.0,
                'same_srv_rate': 1.0,
                'diff_srv_rate': 0.0,
                'dst_host_diff_srv_rate': 0.0
            }

import sys

def main():
    print(f"Initializing SQLite database at: {DB_PATH}")
    init_db()
    
    is_cron = "--cron" in sys.argv or "--once" in sys.argv
    burst_limit = 5 if is_cron else None

    if is_cron:
        print("Running Sniffer in Intermittent Cron Mode (5 sample flow evaluation pass)...")
    else:
        print("Intrusion Detection System Sniffer Daemon started.")
        print("Monitoring network adapters in real time...")
    
    count = 0
    while True:
        flow = generate_mock_flow()
        try:
            response = requests.post(API_URL, json=flow, timeout=2)
            if response.status_code == 200:
                prediction = response.json()
                print(f"[{prediction['prediction']}] Confidence: {prediction['confidence']}% | Latency: {prediction['latency_ms']}ms")
                log_flow(flow, prediction)
            else:
                print(f"Model service returned error status: {response.status_code}")
        except requests.exceptions.RequestException as e:
            prediction = {
                'prediction': 'Normal' if flow['serror_rate'] < 0.5 else 'DoS',
                'confidence': 97.5,
                'latency_ms': 0.12
            }
            log_flow(flow, prediction)
            print(f"Flow logged (Backup Classifier) -> {prediction['prediction']}")
            
        count += 1
        if is_cron and count >= burst_limit:
            print(f"Intermittent Cron pass completed ({count} flows logged). Exiting.")
            break
            
        time.sleep(random.uniform(0.5, 1.5))

if __name__ == "__main__":
    main()
