<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Database, HardDrive, Radio, Server, Zap } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { websockets, database, objectStorage, cache, queue } from '@/routes/demo';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    databaseDriver: string;
    userCount: number;
    cacheDriver: string;
    queueDriver: string;
    storageDriver: string;
    broadcastDriver: string;
    phpVersion: string;
    laravelVersion: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const isConnected = ref(false);

if (typeof window !== 'undefined' && (window as any).Echo) {
    isConnected.value = true;
}

const driverLabel = (driver: string): string => {
    const labels: Record<string, string> = {
        sqlite: 'SQLite',
        mysql: 'MySQL',
        pgsql: 'PostgreSQL',
        mariadb: 'MariaDB',
        redis: 'Redis',
        database: 'Database',
        file: 'File',
        local: 'Local',
        s3: 'S3',
        sqs: 'SQS',
        sync: 'Sync',
        reverb: 'Reverb',
        log: 'Log',
        null: 'None',
        memcached: 'Memcached',
    };

    return labels[driver] ?? driver;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <div>
                <h2 class="text-lg font-semibold">Environment</h2>
                <p class="text-sm text-muted-foreground">
                    PHP {{ phpVersion }} &middot; Laravel {{ laravelVersion }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link :href="database()" class="group">
                    <Card class="transition-shadow group-hover:shadow-md">
                        <CardHeader class="flex flex-row items-center gap-3 space-y-0 pb-2">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Database class="size-5" />
                            </div>
                            <div>
                                <CardTitle class="text-sm font-medium">Database</CardTitle>
                                <CardDescription>Data storage</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold">{{ driverLabel(databaseDriver) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ userCount }} {{ userCount === 1 ? 'user' : 'users' }} registered</p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="websockets()" class="group">
                    <Card class="transition-shadow group-hover:shadow-md">
                        <CardHeader class="flex flex-row items-center gap-3 space-y-0 pb-2">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Radio class="size-5" />
                            </div>
                            <div>
                                <CardTitle class="text-sm font-medium">Websockets</CardTitle>
                                <CardDescription>Real-time broadcasting</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold">{{ driverLabel(broadcastDriver) }}</p>
                            <div class="mt-1 flex items-center gap-1.5">
                                <span
                                    class="size-2 rounded-full"
                                    :class="isConnected ? 'bg-green-500' : 'bg-muted-foreground/40'"
                                />
                                <span class="text-xs text-muted-foreground">
                                    {{ isConnected ? 'Connected' : 'Not connected' }}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="cache()" class="group">
                    <Card class="transition-shadow group-hover:shadow-md">
                        <CardHeader class="flex flex-row items-center gap-3 space-y-0 pb-2">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Zap class="size-5" />
                            </div>
                            <div>
                                <CardTitle class="text-sm font-medium">Cache</CardTitle>
                                <CardDescription>Key-value store</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold">{{ driverLabel(cacheDriver) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">TTL-based expiration</p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="queue()" class="group">
                    <Card class="transition-shadow group-hover:shadow-md">
                        <CardHeader class="flex flex-row items-center gap-3 space-y-0 pb-2">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <Server class="size-5" />
                            </div>
                            <div>
                                <CardTitle class="text-sm font-medium">Queue</CardTitle>
                                <CardDescription>Background jobs</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold">{{ driverLabel(queueDriver) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">Auto-scaling workers</p>
                        </CardContent>
                    </Card>
                </Link>

                <Link :href="objectStorage()" class="group">
                    <Card class="transition-shadow group-hover:shadow-md">
                        <CardHeader class="flex flex-row items-center gap-3 space-y-0 pb-2">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <HardDrive class="size-5" />
                            </div>
                            <div>
                                <CardTitle class="text-sm font-medium">Storage</CardTitle>
                                <CardDescription>File storage</CardDescription>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-2xl font-bold">{{ driverLabel(storageDriver) }}</p>
                            <p class="mt-1 text-xs text-muted-foreground">Upload &amp; serve files</p>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
