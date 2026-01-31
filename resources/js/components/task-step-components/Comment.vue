<script setup lang="ts">
import comment from '@/routes/step/comment';
import { Button } from '../ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '../ui/card';
import { ScrollArea } from '../ui/scroll-area';
import { Textarea } from '../ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { Step } from '@/types';
import { toast } from 'vue-sonner';


const props = defineProps<{
    step: Step
}>();

const commentForm = useForm({
    content: ''
});

const submitComment = () => {
    commentForm.post(comment.store({ task: props.step.task_id, step: props.step.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            // Clear form after successful submission
            commentForm.reset();
            toast.success('Comment posted successfully');
        },
    });
};


</script>
<template>
    <Card class="">
        <form @submit.prevent="comment.store({ task: props.step.task_id, step: props.step.id }).url" class="space-y-4">
            <CardHeader>
                <CardTitle>Comments</CardTitle>
            </CardHeader>
            <CardContent >
                <div>
                    <ScrollArea class="min-h-auto max-h-64">

                        <div class="max-h-48 w-full">
                            <div class="space-y-2 pr-4">
                                <!-- Display actual comments if they exist -->
                                <template v-if="props.step.comments && props.step.comments.length > 0">
                                    <div v-for="stepComment in [...props.step.comments].reverse()" :key="stepComment.id"
                                        class="p-4 bg-zinc-900 rounded border border-zinc-800">
                                        <p class="text-xs text-zinc-300">{{ stepComment.content }}</p>
                                        <p class="text-[10px] text-zinc-500 mt-2">
                                            by <span class="font-medium text-zinc-400">{{ stepComment.user?.name ??
                                                'Someone'
                                            }}</span> on
                                            {{ new Date(stepComment.created_at).toLocaleString() }}
                                        </p>
                                    </div>
                                </template>

                                <!-- Empty state -->
                                <div v-else class="text-center py-8">
                                    <p class="text-xs text-zinc-500">No comments yet. Be the first to comment!</p>
                                </div>
                            </div>
                        </div>
                    </ScrollArea>
                </div>
            </CardContent>
            <CardFooter class="pt-4 w-full" v-if="props.step.status !== 'completed'">
                <div class="space-y-2 flex flex-col w-full">
                    <Textarea placeholder="Add a comment..." v-model="commentForm.content"
                        class="bg-zinc-900/50 resize-none" />
                    <Button size="sm" class="ml-auto" @click="submitComment">Post Comment</Button>
                </div>
            </CardFooter>
        </form>
    </Card>
</template>