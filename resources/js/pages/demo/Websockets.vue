<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { websockets } from '@/routes/demo';
import { send } from '@/actions/App/Http/Controllers/Demo/WebsocketsController';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Websockets', href: websockets() },
];

type Message = {
    username: string;
    message: string;
    timestamp: string;
};

const props = defineProps<{
    messages: Message[];
}>();

const realtimeMessages = ref<Message[]>([]);
const isConnected = ref(!!import.meta.env.VITE_REVERB_APP_KEY);

const allMessages = computed(() => [...props.messages, ...realtimeMessages.value]);

const form = useForm({ message: '' });

const sendMessage = () => {
    form.post(send.url(), {
        preserveScroll: true,
        onSuccess: () => form.reset('message'),
    });
};

useEcho('demo', ['DemoMessageSent'], (e: Message) => {
    realtimeMessages.value.push(e);
    isConnected.value = true;
});
</script>

<template>
    <Head title="Websockets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Real-time Broadcasting</CardTitle>
                        <CardDescription>
                            Laravel Cloud provides managed Reverb websocket servers for real-time communication.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-4 flex items-center gap-2">
                            <div
                                class="h-2.5 w-2.5 rounded-full"
                                :class="isConnected ? 'bg-green-500' : 'bg-red-500'"
                            />
                            <span class="text-sm text-muted-foreground">
                                {{ isConnected ? 'Connected to Reverb' : 'Not connected - configure Echo to enable' }}
                            </span>
                        </div>

                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <Input
                                v-model="form.message"
                                placeholder="Type a message..."
                                class="flex-1"
                            />
                            <Button type="submit" :disabled="form.processing || !form.message">
                                <Spinner v-if="form.processing" />
                                Send
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Messages</CardTitle>
                        <CardDescription>
                            Messages broadcast via Reverb appear here in real-time.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="max-h-64 space-y-2 overflow-y-auto" v-if="allMessages.length">
                            <div
                                v-for="(msg, i) in allMessages"
                                :key="i"
                                class="rounded-lg bg-muted p-3"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">{{ msg.username }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        {{ new Date(msg.timestamp).toLocaleTimeString() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm">{{ msg.message }}</p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-muted-foreground">
                            No messages yet. Send a message to see real-time broadcasting in action.
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>How It Works</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        <p class="text-sm text-muted-foreground">
                            Laravel Cloud includes managed <strong>Reverb</strong> websocket servers. Events are broadcast from the server using Laravel's broadcasting system, and received by the frontend using Laravel Echo. This enables real-time features like live notifications, chat, and collaborative editing.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
