<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Task } from '@/types';

const props = defineProps<{
    task: {data: Task}
}>();

</script>
<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button size="sm">Add step</Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Add step</DialogTitle>
                <DialogDescription>add step to task</DialogDescription>
            </DialogHeader>
            <div></div>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="ghost" >Discard</Button>
                </DialogClose>
                <Button>Add step</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>