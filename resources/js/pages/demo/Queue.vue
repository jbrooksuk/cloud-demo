<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { queue } from '@/routes/demo';
import { dispatch as dispatchAction } from '@/actions/App/Http/Controllers/Demo/QueueController';
import type { BreadcrumbItem } from '@/types';

type Job = {
    id: number;
    status: string;
    started_at?: string;
    completed_at?: string;
};

const props = defineProps<{
    jobs: Job[];
    queueDriver: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Queue Workers', href: queue() },
];

const dispatching = ref(false);
const realtimeUpdates = ref<Map<number, Partial<Job>>>(new Map());

const mergedJobs = computed(() =>
    props.jobs.map((job) => {
        const update = realtimeUpdates.value.get(job.id);
        return update ? { ...job, ...update } : job;
    }),
);

const dispatchJob = () => {
    dispatching.value = true;
    router.post(dispatchAction.url(), {}, {
        preserveScroll: true,
        onFinish: () => {
            dispatching.value = false;
        },
    });
};

type JobUpdate = {
    id: number;
    status: string;
    startedAt?: string;
    completedAt?: string;
};

useEcho('demo', ['DemoJobUpdated'], (e: JobUpdate) => {
    realtimeUpdates.value.set(e.id, {
        status: e.status,
        started_at: e.startedAt,
        completed_at: e.completedAt,
    });
});

const statusColor = (status: string): string => {
    switch (status) {
        case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        case 'processing': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        default: return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    }
};
</script>

<template>
    <Head title="Queue Workers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Driver</CardTitle>
                        <CardDescription>Current queue driver</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ queueDriver }}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Dispatch a Job</CardTitle>
                        <CardDescription>Send a demo job to the queue</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button @click="dispatchJob" :disabled="dispatching">
                            <Spinner v-if="dispatching" />
                            Dispatch Job
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Recent Jobs</CardTitle>
                    <CardDescription>Status updates arrive in real-time via websockets</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2" v-if="mergedJobs.length">
                        <div
                            v-for="job in mergedJobs"
                            :key="job.id"
                            class="flex items-center justify-between rounded-md bg-muted px-3 py-2"
                        >
                            <div>
                                <p class="font-mono text-sm">#{{ job.id }}</p>
                                <p class="text-xs text-muted-foreground" v-if="job.completed_at">
                                    Completed {{ new Date(job.completed_at).toLocaleTimeString() }}
                                </p>
                            </div>
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="statusColor(job.status)"
                            >
                                {{ job.status }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        No jobs dispatched yet. Click "Dispatch Job" to send a job to the queue.
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>How It Works</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground">
                        Laravel Cloud provides managed <strong>queue workers</strong> that process your queued jobs. Jobs are dispatched using Laravel's queue system and processed by dedicated worker processes. Status updates are broadcast in real-time via <strong>Reverb</strong> websockets.
                    </p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
