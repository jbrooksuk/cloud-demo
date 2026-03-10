<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useEchoPublic } from '@laravel/echo-vue';
import axios from 'axios';
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { websockets } from '@/routes/demo';
import { send, typing as typingAction } from '@/actions/App/Http/Controllers/Demo/WebsocketsController';
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

const typingUsers = ref<Map<string, ReturnType<typeof setTimeout>>>(new Map());
const typingNames = computed(() => [...typingUsers.value.keys()]);
let typingDebounce: ReturnType<typeof setTimeout> | null = null;

const onInput = () => {
    if (typingDebounce) clearTimeout(typingDebounce);
    typingDebounce = setTimeout(() => {
        axios.post(typingAction.url());
    }, 300);
};

onUnmounted(() => {
    if (typingDebounce) clearTimeout(typingDebounce);
    typingUsers.value.forEach(timer => clearTimeout(timer));
});

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

useEchoPublic('demo', ['DemoUserTyping'], (e: { username: string }) => {
    const existing = typingUsers.value.get(e.username);
    if (existing) clearTimeout(existing);
    const timer = setTimeout(() => {
        typingUsers.value.delete(e.username);
    }, 2000);
    typingUsers.value.set(e.username, timer);
});

useEchoPublic('demo', ['DemoMessageSent'], (e: Message) => {
    const existingTimer = typingUsers.value.get(e.username);
    if (existingTimer) {
        clearTimeout(existingTimer);
        typingUsers.value.delete(e.username);
    }
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
                            class="mb-4 max-h-[calc(100vh-20rem)] space-y-2 overflow-y-auto"
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

                        <p v-if="typingNames.length" class="mb-2 text-xs text-muted-foreground italic">
                            {{ typingNames.join(', ') }} {{ typingNames.length === 1 ? 'is' : 'are' }} typing...
                        </p>

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
                                    @input="onInput"
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
