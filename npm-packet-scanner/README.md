# unesco-mine-sec-cli

An open-source network flow telemetry client for the **Securing the Digital Mine** intrusion detection system. 

It sniffs active local interfaces, extracts the 10 selected features optimized via Binary Whale Optimization Algorithm (BWOA), and streams them in real-time to your dashboard's REST API for CNN-LSTM anomaly classification.

## Installation

You can run it instantly using `npx`:

```bash
npx unesco-mine-sec-cli
```

Or install it globally:

```bash
npm install -g unesco-mine-sec-cli
unesco-mine-sec-cli
```

## Features Monitored
- **protocol_type**
- **service**
- **flag**
- **src_bytes**
- **hot**
- **su_attempted**
- **serror_rate**
- **same_srv_rate**
- **diff_srv_rate**
- **dst_host_diff_srv_rate**

## License
MIT
