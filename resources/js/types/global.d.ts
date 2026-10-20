import type { Auth } from '@/types/auth';
import { FlashAction, type FlashToast } from '@/types/flash';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(
            pattern: string,
            options?: { eager?: boolean },
        ) => Record<string, T>;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            // Digital Judge pages only (app.ts); Management pages get `manage` instead
            judge: {
                isHeadRef: boolean
            }
            // Management app pages only (manage.ts)
            manage?: {
                isAdmin: boolean
                // full urls of the public site on the main domain
                site: {
                    home: string
                    explore: string
                }
                // the Blade pages' session flashes: `status` (e.g. password reset) and `alert-error`
                flash: {
                    status: string | null
                    error: string | null
                }
            }
            env_local: boolean
            // full urls of the Terms, Privacy and Cookie policies (Blade pages on the main domain)
            legal: {
                terms: string
                privacy: string
                cookies: string
                tradingAs: string
                contactEmail: string
            }
            [key: string]: unknown;
        };

        flashDataType: {
            action?: FlashAction
            toast?: FlashToast
        }
    }
}