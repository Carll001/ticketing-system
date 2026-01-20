<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Button } from '../ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { ref } from 'vue';
import { AlertTriangle } from 'lucide-vue-next';
import { User } from '@/types';
import { toast, Toaster } from 'vue-sonner';
const openDelete = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    user: User;
}>();

const deleteUser = () => {
    isDeleting.value = true;
    router.delete(`/user/${props.user.id}`, {
        onFinish: () => {
            toast.success('User deleted sucessfully!')
            isDeleting.value = false;
            openDelete.value = false;
        }
    });
}
</script>
<template>
    <Dialog v-model:open="openDelete">
        <DialogTrigger as-child>
            <Button size="sm" variant="destructive">Delete</Button>
        </DialogTrigger>
        <DialogContent class="max-w-md">
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <DialogTitle>Delete User</DialogTitle>
                </div>
                <DialogDescription class="mt-3">
                    Are you sure you want to delete? This action cannot be undone.
                </DialogDescription>
            </DialogHeader>

            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="ghost" type="button">
                        Cancel
                    </Button>
                </DialogClose>
                <Button variant="destructive" @click="deleteUser" :disabled="isDeleting">
                    {{ isDeleting ? 'Deleting...' : 'Delete' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
