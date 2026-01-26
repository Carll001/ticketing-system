<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Button } from '../ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { ref } from 'vue';
import { AlertTriangle } from 'lucide-vue-next';
import { User } from '@/types';
import { toast, Toaster } from 'vue-sonner';
import user from '@/routes/user';

const closeDeleteModal = ref(false);
const loadingDelete = ref(false);

const props = defineProps<{
    user: User;
}>();

const deleteUser = () => {
    router.delete(user.destroy.url(props.user.id),{
        onSuccess: ()=>{
            toast.success('User deleted successfulyy!')
            loadingDelete.value = false
            closeDeleteModal.value = false
        }
    })
}
</script>
<template>
    <Dialog v-model:open="closeDeleteModal">
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
                <Button variant="destructive" @click="deleteUser" :disabled="loadingDelete">
                    {{ loadingDelete ? 'Deleting...' : 'Delete' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
