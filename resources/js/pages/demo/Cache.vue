<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import axios from 'axios';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { cache } from '@/routes/demo';
import { store, increment as incrementAction, flush as flushAction } from '@/actions/App/Http/Controllers/Demo/CacheController';
import type { BreadcrumbItem } from '@/types';

type CacheEntry = {
    key: string;
    value: string | null;
    exists: boolean;
};

const props = defineProps<{
    entries: CacheEntry[];
    cacheDriver: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Cache', href: cache() },
];

const allEntries = ref<CacheEntry[]>([...props.entries]);
const incrementing = ref(false);
const flushing = ref(false);
const storing = ref(false);
const formKey = ref('demo:message');
const formValue = ref('');
const formTtl = ref(60);

const updateEntries = (entries: CacheEntry[]) => {
    allEntries.value = entries;
};

const incrementCounter = async () => {
    incrementing.value = true;
    try {
        const { data } = await axios.post(incrementAction.url());
        updateEntries(data.entries);
    } finally {
        incrementing.value = false;
    }
};

const flushCache = async () => {
    flushing.value = true;
    try {
        const { data } = await axios.delete(flushAction.url());
        updateEntries(data.entries);
    } finally {
        flushing.value = false;
    }
};

const storeValue = async () => {
    storing.value = true;
    try {
        const { data } = await axios.post(store.url(), {
            key: formKey.value,
            value: formValue.value,
            ttl: formTtl.value,
        });
        updateEntries(data.entries);
        formValue.value = '';
    } finally {
        storing.value = false;
    }
};

useEchoPublic('demo', ['DemoCacheUpdated'], (e: { entries: CacheEntry[] }) => {
    updateEntries(e.entries);
});
</script>

<template>
    <Head title="Cache" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Driver</CardTitle>
                        <CardDescription>Current cache driver</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ cacheDriver }}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Counter</CardTitle>
                        <CardDescription>Atomic cache increment demo</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">
                            {{ allEntries.find(e => e.key === 'demo:counter')?.value ?? 0 }}
                        </p>
                        <Button size="sm" class="mt-2" :disabled="incrementing" @click="incrementCounter">
                            <Spinner v-if="incrementing" />
                            Increment
                        </Button>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Actions</CardTitle>
                        <CardDescription>Manage demo cache entries</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button variant="destructive" size="sm" :disabled="flushing" @click="flushCache">
                            <Spinner v-if="flushing" />
                            Flush Demo Cache
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Store a Value</CardTitle>
                        <CardDescription>Write a key-value pair to the cache</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="storeValue" class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="cache-key">Key</Label>
                                <Input id="cache-key" v-model="formKey" placeholder="demo:message" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="cache-value">Value</Label>
                                <Input id="cache-value" v-model="formValue" placeholder="Hello, Cloud!" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="cache-ttl">TTL (seconds)</Label>
                                <Input id="cache-ttl" v-model.number="formTtl" type="number" min="1" max="3600" />
                            </div>
                            <Button type="submit" :disabled="storing">
                                <Spinner v-if="storing" />
                                Store
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Cached Entries</CardTitle>
                        <CardDescription>Current values in the cache</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div
                                v-for="entry in allEntries"
                                :key="entry.key"
                                class="rounded-md bg-muted px-3 py-2"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-sm font-medium">{{ entry.key }}</span>
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs"
                                        :class="entry.exists ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                                    >
                                        {{ entry.exists ? 'HIT' : 'MISS' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ entry.value ?? 'null' }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>How It Works</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground">
                        Laravel Cloud provides managed <strong>Redis</strong> instances for caching. Your application uses Laravel's Cache facade with the Redis driver, giving you fast, distributed caching with support for atomic operations, tags, and TTL-based expiration.
                    </p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
