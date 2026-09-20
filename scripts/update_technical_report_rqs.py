import docx

def run():
    path = 'research/technical_report.docx'
    doc = docx.Document(path)
    
    # Check if RQ1 is already there
    full_text = '\n'.join([p.text for p in doc.paragraphs])
    if 'RQ1' in full_text:
        print("RQ1 already in technical_report.docx")
        return
        
    rq_text = (
        "1.3 Formal Research Questions\n"
        "To establish rigorous empirical grounding under the Design Science Research methodology, the technical investigation addresses four core research questions:\n\n"
        "- RQ1 (Dimensionality Optimization): To what extent can a constrained Binary Whale Optimization Algorithm (BWOA) with an adaptive alpha decay schedule and a hard accuracy floor prune high-dimensional industrial telemetry features while preserving multi-class threat discrimination?\n"
        "- RQ2 (Spatial-Temporal Threat Modeling): How effectively does a hybrid 1D Convolutional Neural Network and Long Short-Term Memory (Conv1D-LSTM) architecture capture packet-level spatial correlations and sequential connection state transitions in industrial SCADA networks?\n"
        "- RQ3 (Edge Real-Time Execution and Quantization): Can post-training Float16 quantization compress the spatial-temporal neural network below 1.0 MB and achieve sub-millisecond (<1.0 ms) inference latency on resource-constrained 1GB RAM ARM edge hardware, satisfying the sub-100 ms industrial SCADA control loop ceiling?\n"
        "- RQ4 (Empirical Generalization, Transferability, and Economic Impact): How robustly does the framework generalize across physical industrial SCADA testbeds (such as the 51-sensor SWaT testbed), and what is its operational and economic return on investment (ROI) in mitigating industrial downtime and preserving human life in mineral extraction operations?"
    )
    
    # Insert right after Paragraph 7
    for i, p in enumerate(doc.paragraphs):
        if 'This technical report details the architecture' in p.text:
            # We can append the RQ text to p or add after
            p.text = p.text + "\n\n" + rq_text
            print(f"Added RQs to paragraph {i}")
            break
            
    doc.save(path)
    print("technical_report.docx updated successfully.")

if __name__ == '__main__':
    run()
