FROM python:3.11-slim

WORKDIR /opt/unesco-project

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpcap-dev gcc curl procps time \
    && rm -rf /var/lib/apt/lists/*

COPY repo /opt/unesco-project

RUN pip install --no-cache-dir -r requirements.txt

# Try to install the lightweight edge inference runtime the docs specify for the Pi.
# Fall back is handled at test/report time if this wheel isn't available for this arch/py version.
RUN pip install --no-cache-dir tflite-runtime 2>/opt/unesco-project/tflite_runtime_install.log || \
    echo "tflite-runtime install FAILED, see tflite_runtime_install.log" >> /opt/unesco-project/tflite_runtime_install.log

CMD ["/bin/bash"]
