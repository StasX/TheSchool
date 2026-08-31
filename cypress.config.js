import { defineConfig } from 'cypress';
import dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
    e2e: {
        baseUrl: process.env.APP_URL ?? 'http://localhost:8090',
    },
});