# @mhiskall282/unesco-mine-sec-cli

An open-source network flow telemetry client for the **Securing the Digital Mine** intrusion detection system. 

It sniffs active local interfaces, extracts the 10 selected features optimized via Binary Whale Optimization Algorithm (BWOA), and streams them in real-time to your dashboard's REST API for CNN-LSTM anomaly classification.

## Installation via GitHub Packages

Configure npm to use GitHub Packages for the `@mhiskall282` scope:

```bash
# Configure GitHub Packages registry for this scope
npm config set @mhiskall282:registry https://npm.pkg.github.com

# Run directly via npx
npx @mhiskall282/unesco-mine-sec-cli

# Or install globally
npm install -g @mhiskall282/unesco-mine-sec-cli
unesco-mine-sec-cli
```

## Local Installation

```bash
cd npm-packet-scanner
npm install && npm install -g ./
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
