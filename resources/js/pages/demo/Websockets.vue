<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import axios from 'axios';
import { nextTick, onMounted, ref } from 'vue';
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

const allMessages = ref<Message[]>([...props.messages]);
const isConnected = ref(!!import.meta.env.VITE_REVERB_APP_KEY);
const messagesContainer = ref<HTMLElement | null>(null);
const message = ref('');
const sending = ref(false);

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

onMounted(() => scrollToBottom());

const sendMessage = async () => {
    if (!message.value || sending.value) return;

    sending.value = true;
    try {
        const { data } = await axios.post(send.url(), { message: message.value });
        message.value = '';
        if (data.timestamp && !allMessages.value.some(m => m.timestamp === data.timestamp && m.username === data.username)) {
            allMessages.value.push({ username: data.username, message: data.message, timestamp: data.timestamp });
            scrollToBottom();
        }
    } finally {
        sending.value = false;
    }
};

useEcho('demo', ['DemoMessageSent'], (e: Message) => {
    if (!allMessages.value.some(m => m.timestamp === e.timestamp && m.username === e.username)) {
        allMessages.value.push(e);
        scrollToBottom();
    }
    isConnected.value = true;
});
</script>

<template>
    <Head title="Websockets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 lg:grid-cols-2">
                <Card class="flex flex-col">
                    <CardHeader>
                        <CardTitle>Messages</CardTitle>
                        <CardDescription>
                            All visitors see messages in real-time via Reverb.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-1 flex-col">
                        <div
                            ref="messagesContainer"
                            class="mb-4 flex-1 space-y-2 overflow-y-auto"
                            :class="allMessages.length ? 'min-h-[24rem]' : ''"
                        >
                            <template v-if="allMessages.length">
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
                            </template>
                            <p v-else class="text-sm text-muted-foreground">
                                No messages yet. Send a message to see real-time broadcasting in action.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 border-t pt-4">
                            <div
                                class="size-2.5 shrink-0 rounded-full"
                                :class="isConnected ? 'bg-green-500' : 'bg-red-500'"
                            />
                            <form @submit.prevent="sendMessage" class="flex flex-1 gap-2">
                                <Input
                                    v-model="message"
                                    placeholder="Type a message..."
                                    class="flex-1"
                                />
                                <Button type="submit" :disabled="sending || !message">
                                    <Spinner v-if="sending" />
                                    Send
                                </Button>
                            </form>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>How It Works</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3 text-sm text-muted-foreground">
                            <p>
                                Laravel Cloud includes managed <strong>Reverb</strong> websocket servers. Events are broadcast from the server using Laravel's broadcasting system, and received by all connected clients using Laravel Echo.
                            </p>
                            <p>
                                Open this page in multiple tabs or browsers to see messages appear everywhere in real-time. Messages are persisted to the database so history is preserved across page loads.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
