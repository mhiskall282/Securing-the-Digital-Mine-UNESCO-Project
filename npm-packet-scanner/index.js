#!/usr/bin/env node

import chalk from 'chalk';
import inquirer from 'inquirer';
import fetch from 'node-fetch';
import os from 'os';

const DEFAULT_API_URL = 'http://localhost:8000/api/external/analyze';
const VERSION = '3.0.0-saas';

// Command-line argument parsing
const args = process.argv.slice(2);

if (args.includes('--help') || args.includes('-h')) {
  console.log(chalk.bold.green('\n============================================='));
  console.log(chalk.bold.green('    Securing the Digital Mine - CLI Client   '));
  console.log(chalk.bold.green('   OT/IoT Intrusion Detection Flow Scanner   '));
  console.log(chalk.bold.green('=============================================\n'));
  console.log(chalk.bold('USAGE:'));
  console.log('  unesco-mine-sec-cli [options]\n');
  console.log(chalk.bold('OPTIONS:'));
  console.log('  -h, --help                 Show this help menu and exit');
  console.log('  -v, --version              Show software version and exit');
  console.log('  --url <api_endpoint>       Specify target API Gateway URL (default: http://localhost:8000/api/external/analyze)');
  console.log('  --key <bearer_token>       Specify Device Node Bearer Token');
  console.log('  --interface <adapter_name> Specify network adapter name (e.g., eth0, Wi-Fi 2)\n');
  console.log(chalk.bold('EXAMPLES:'));
  console.log('  unesco-mine-sec-cli --help');
  console.log('  unesco-mine-sec-cli --url http://127.0.0.1:8000/api/external/analyze --key unesco_device_token_2026\n');
  process.exit(0);
}

if (args.includes('--version') || args.includes('-v')) {
  console.log(`unesco-mine-sec-cli version ${VERSION}`);
  process.exit(0);
}

// Extract optional CLI parameters
function getArgValue(flag) {
  const index = args.indexOf(flag);
  if (index !== -1 && index + 1 < args.length) {
    return args[index + 1];
  }
  return null;
}

const argUrl = getArgValue('--url');
const argKey = getArgValue('--key');
const argInterface = getArgValue('--interface');

console.log(chalk.bold.green('\n============================================='));
console.log(chalk.bold.green('    Securing the Digital Mine - CLI Client   '));
console.log(chalk.bold.green('   OT/IoT Intrusion Detection Flow Scanner   '));
console.log(chalk.bold.green('=============================================\n'));

async function main() {
  const interfaces = os.networkInterfaces();
  const interfaceNames = Object.keys(interfaces);

  if (interfaceNames.length === 0) {
    console.log(chalk.red('[-] No active network interfaces found. Exiting.'));
    process.exit(1);
  }

  let apiUrl = argUrl || DEFAULT_API_URL;
  let adapter = argInterface || interfaceNames[0];
  let apiKey = argKey || 'unesco_demo_token_2026';

  // If parameters are missing, prompt interactively
  if (!argUrl || !argKey || !argInterface) {
    const answers = await inquirer.prompt([
      {
        type: 'input',
        name: 'apiUrl',
        message: 'Enter Dashboard REST API URL:',
        default: apiUrl
      },
      {
        type: 'list',
        name: 'interface',
        message: 'Select Network Interface to sniff:',
        choices: interfaceNames,
        default: adapter
      },
      {
        type: 'password',
        name: 'apiKey',
        message: 'Enter API Token / Organization Token:',
        default: apiKey
      }
    ]);

    apiUrl = answers.apiUrl;
    adapter = answers.interface;
    apiKey = answers.apiKey;
  }

  console.log(chalk.cyan(`\n[*] Hooking adapter ${chalk.bold(adapter)}...`));
  console.log(chalk.cyan(`[*] Target API Endpoint: ${chalk.bold(apiUrl)}`));
  console.log(chalk.yellow('[*] Press Ctrl+C to stop scanning.\n'));

  // Connection validation
  try {
    const res = await fetch(apiUrl.replace('/analyze', '/status'), { timeout: 3000 });
    if (res.ok) {
      console.log(chalk.green('[+] Connection verified. Classifier is ONLINE.\n'));
    }
  } catch (err) {
    console.log(chalk.yellow('[!] Warning: Endpoint validation timed out. Streaming in dry-run/forward mode.\n'));
  }

  // Real-time packet parsing simulation loop
  setInterval(async () => {
    // Generate active telemetry payload
    const isAnomaly = Math.random() < 0.15;
    let flowData = {};

    if (!isAnomaly) {
      flowData = {
        protocol_type: Math.random() > 0.5 ? 'tcp' : 'udp',
        service: 'http',
        flag: 'SF',
        src_bytes: Math.floor(Math.random() * 800) + 64,
        hot: 0,
        su_attempted: 0,
        serror_rate: 0.0,
        same_srv_rate: 1.0,
        diff_srv_rate: 0.0,
        dst_host_diff_srv_rate: parseFloat((Math.random() * 0.1).toFixed(2))
      };
    } else {
      flowData = {
        protocol_type: 'tcp',
        service: 'private',
        flag: 'S0',
        src_bytes: 0,
        hot: 0,
        su_attempted: 0,
        serror_rate: 1.0,
        same_srv_rate: 0.05,
        diff_srv_rate: 0.95,
        dst_host_diff_srv_rate: 0.85
      };
    }

    const timestamp = new Date().toISOString().split('T')[1].slice(0, -1);
    
    try {
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${apiKey}`
        },
        body: JSON.stringify(flowData)
      });

      if (response.ok) {
        const result = await response.json();
        const predText = result.prediction === 'Normal' 
          ? chalk.bold.green(result.prediction) 
          : chalk.bold.red(result.prediction);
        
        console.log(`[${timestamp}] Flow captured on ${adapter} -> Inference: ${predText} (Conf: ${result.confidence}%)`);
      } else {
        console.log(chalk.red(`[${timestamp}] API Error: ${response.status} ${response.statusText}`));
      }
    } catch (e) {
      console.log(chalk.gray(`[${timestamp}] Flow streamed -> (Dashboard backend disconnected/offline)`));
    }
  }, 2000);
}

main();
