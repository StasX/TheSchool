import { defineConfig } from 'cypress';
import dotenv from 'dotenv';

dotenv.config();

export default defineConfig({
    e2e: {
        baseUrl: process.env.APP_URL ?? 'http://localhost:8090',

        env: {
            OWNER_EMAIL: process.env.CYPRESS_OWNER_EMAIL,
            OWNER_PASSWORD: process.env.CYPRESS_OWNER_PASSWORD,
            MANAGER_EMAIL: process.env.CYPRESS_MANAGER_EMAIL,
            MANAGER_PASSWORD: process.env.CYPRESS_MANAGER_PASSWORD,
            SALES_EMAIL: process.env.CYPRESS_SALES_EMAIL,
            SALES_PASSWORD: process.env.CYPRESS_SALES_PASSWORD,
        },
    },
});