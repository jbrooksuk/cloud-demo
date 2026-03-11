<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Exception Demo', href: '/demo/exception' },
];
</script>

<template>
    <Head title="Exception Demo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 items-center justify-center rounded-xl p-4">
            <Card class="max-w-md text-center">
                <CardHeader>
                    <CardTitle class="text-3xl">You did it, thanks AI!</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground">
                        The exception has been resolved. This page now renders successfully.
                    </p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
