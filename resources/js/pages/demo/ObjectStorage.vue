<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { objectStorage } from '@/routes/demo';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    files: Array<{
        name: string;
        path: string;
        size: number;
        lastModified: string;
        url: string;
    }>;
    diskDriver: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Object Storage', href: objectStorage() },
];

const fileInput = ref<HTMLInputElement>();
const uploading = ref(false);
const selectedFile = ref<File | null>(null);

const onFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    selectedFile.value = input.files?.[0] ?? null;
};

const uploadFile = () => {
    if (!selectedFile.value) return;

    const form = useForm({ file: selectedFile.value });
    uploading.value = true;

    form.post('/demo/object-storage', {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            uploading.value = false;
            selectedFile.value = null;
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const deleteFile = (path: string) => {
    router.delete('/demo/object-storage', {
        data: { path },
        preserveScroll: true,
    });
};

const formatSize = (bytes: number): string => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};
</script>

<template>
    <Head title="Object Storage" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Upload File</CardTitle>
                        <CardDescription>
                            Upload a file to the {{ diskDriver }} storage driver.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex gap-2">
                            <Input
                                ref="fileInput"
                                type="file"
                                class="flex-1"
                                @change="onFileChange"
                            />
                            <Button :disabled="uploading || !selectedFile" @click="uploadFile">
                                <Spinner v-if="uploading" />
                                Upload
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Storage Driver</CardTitle>
                        <CardDescription>Current filesystem configuration</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ diskDriver }}</p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ files.length }} file{{ files.length !== 1 ? 's' : '' }} stored
                        </p>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Stored Files</CardTitle>
                    <CardDescription>Files in the demo storage directory</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2" v-if="files.length">
                        <div
                            v-for="file in files"
                            :key="file.path"
                            class="flex items-center justify-between rounded-md bg-muted px-3 py-2"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ file.name }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ formatSize(file.size) }} &middot; {{ file.lastModified }}
                                </p>
                            </div>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="deleteFile(file.path)"
                            >
                                Delete
                            </Button>
                        </div>
                    </div>
                    <p v-else class="text-sm text-muted-foreground">
                        No files uploaded yet. Upload a file to see object storage in action.
                    </p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>How It Works</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground">
                        Laravel Cloud provides managed <strong>S3-compatible object storage</strong>. Files are stored using Laravel's filesystem abstraction, so you can use the same Storage facade and disk configuration you already know. Cloud handles bucket provisioning and CDN distribution.
                    </p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
