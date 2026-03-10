<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { database } from '@/routes/demo';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    userCount: number;
    latestUsers: Array<{
        id: number;
        name: string;
        email: string;
        created_at: string;
    }>;
    databaseDriver: string;
    tables: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Database', href: database() },
];
</script>

<template>
    <Head title="Database" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Driver</CardTitle>
                        <CardDescription>Current database driver</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ databaseDriver }}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Users</CardTitle>
                        <CardDescription>Total registered users</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ userCount }}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Tables</CardTitle>
                        <CardDescription>Database tables count</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ tables.length }}</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Database Tables</CardTitle>
                        <CardDescription>All tables in the database</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-1">
                            <div
                                v-for="table in tables"
                                :key="table"
                                class="rounded-md bg-muted px-3 py-2 font-mono text-sm"
                            >
                                {{ table }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Latest Users</CardTitle>
                        <CardDescription>Most recently registered users</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3" v-if="latestUsers.length">
                            <div
                                v-for="user in latestUsers"
                                :key="user.id"
                                class="flex items-center justify-between rounded-md bg-muted px-3 py-2"
                            >
                                <div>
                                    <p class="text-sm font-medium">{{ user.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                </div>
                                <span class="text-xs text-muted-foreground">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">No users yet.</p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>How It Works</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground">
                        Laravel Cloud provides managed <strong>MySQL</strong> and <strong>PostgreSQL</strong> databases. Your application connects using standard Eloquent models and migrations. Cloud handles provisioning, backups, and scaling automatically.
                    </p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
