# OpenAI AI Summary Setup Guide

## Overview
The AI Summary feature allows property managers to generate intelligent summaries of their maintenance reports using OpenAI's GPT models. The system analyzes maintenance requests, comments, performance metrics, and generates comprehensive insights.

## Setup Instructions

### 1. Get OpenAI API Key
1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Create an account or log in
3. Generate a new API key
4. Copy the API key (it starts with `sk-`)

### 2. Configure Environment Variables
Add the following to your `.env` file:

```env
OPENAI_API_KEY=sk-your-api-key-here
OPENAI_DEFAULT_MODEL=gpt-3.5-turbo
OPENAI_REQUEST_TIMEOUT=30
```

**Note:** You can use `gpt-4` for better results if you have access, but `gpt-3.5-turbo` is more cost-effective.

### 3. Test the Integration
1. Generate a maintenance report with some data
2. Click the "🧠 Generate AI Summary" button
3. The system should display an AI-generated summary

## Features

### What the AI Summary Includes:
- **Overall Statistics**: Total requests, completion rates, average response times
- **Status Analysis**: Breakdown of pending, completed, and in-progress tasks
- **Priority Assessment**: Analysis of high, medium, and low priority requests
- **Performance Insights**: Technician performance and unusual delays
- **Recurring Issues**: Identification of patterns in maintenance requests
- **Comments Analysis**: Key insights from manager and technician comments

### AI Prompt Template
The system uses this base prompt:
> "You are an assistant helping a property manager summarize maintenance reports. Based on the data, and comments inside each task from either the property manager or the technician, create a clear and professional summary. Highlight the most important details such as total number of tasks, completed vs pending, any recurring issues, top-performing technicians, and any unusual delays. Be concise, use plain language, and include relevant stats where helpful."

## Cost Considerations
- **gpt-3.5-turbo**: ~$0.002 per 1K tokens (very affordable)
- **gpt-4**: ~$0.03 per 1K tokens (higher quality, more expensive)
- Average report summary costs: $0.01-0.05 per summary

## Troubleshooting

### Common Issues:
1. **"Failed to generate AI summary"**
   - Check your OpenAI API key is valid
   - Ensure you have sufficient API credits
   - Verify internet connectivity

2. **"Network error"**
   - Check server internet connection
   - Verify OpenAI service status

3. **"Unauthorized"**
   - Only Property Managers and Admins can generate AI summaries
   - Check user permissions

### Error Logs
Check Laravel logs for detailed error messages:
```bash
tail -f storage/logs/laravel.log
```

## Security Notes
- Never commit your OpenAI API key to version control
- Use environment variables for sensitive configuration
- The AI Summary feature includes user access controls
- All API requests are logged for troubleshooting

## Usage Guidelines
- Generate summaries for reports with meaningful data (at least a few maintenance requests)
- The AI works best with reports that include comments from technicians and managers
- Summaries are generated in real-time and are not stored in the database
- Each summary generation makes a fresh API call to OpenAI 