import { Head, Link, useForm } from '@inertiajs/react';
import { type FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { BreadcrumbItem, Department, TicketCategory } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tickets', href: '/tickets' },
    { title: 'Create Ticket', href: '/tickets/create' },
];

interface Props {
    departments: Department[];
    categories: TicketCategory[];
}

export default function TicketsCreate({ departments, categories }: Props) {
    const { data, setData, post, processing, errors } = useForm<{
        subject: string;
        description: string;
        department_id: string;
        category_id: string;
        priority: string;
        attachments: File[];
    }>({
        subject: '',
        description: '',
        department_id: '',
        category_id: '',
        priority: 'medium',
        attachments: [],
    });

    const filteredCategories = data.department_id
        ? categories.filter(
              (cat) => cat.department_id === null || cat.department_id === Number(data.department_id),
          )
        : categories;

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        post('/tickets', {
            forceFormData: true,
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Ticket" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">Create Ticket</h1>
                    <Button variant="outline" asChild>
                        <Link href="/tickets">Back to Tickets</Link>
                    </Button>
                </div>

                <form onSubmit={handleSubmit} className="mx-auto w-full max-w-2xl space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="subject">Subject</Label>
                        <Input
                            id="subject"
                            value={data.subject}
                            onChange={(e) => setData('subject', e.target.value)}
                            placeholder="Brief description of your issue"
                            aria-invalid={!!errors.subject}
                        />
                        {errors.subject && (
                            <p className="text-sm text-destructive">{errors.subject}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="description">Description</Label>
                        <Textarea
                            id="description"
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            placeholder="Describe your issue in detail..."
                            className="min-h-32"
                            aria-invalid={!!errors.description}
                        />
                        {errors.description && (
                            <p className="text-sm text-destructive">{errors.description}</p>
                        )}
                    </div>

                    <div className="grid gap-6 sm:grid-cols-2">
                        <div className="space-y-2">
                            <Label>Department</Label>
                            <Select
                                value={data.department_id}
                                onValueChange={(value) => {
                                    setData('department_id', value);
                                    setData('category_id', '');
                                }}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Select department" />
                                </SelectTrigger>
                                <SelectContent>
                                    {departments.map((dept) => (
                                        <SelectItem key={dept.id} value={String(dept.id)}>
                                            {dept.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.department_id && (
                                <p className="text-sm text-destructive">{errors.department_id}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label>Category</Label>
                            <Select
                                value={data.category_id}
                                onValueChange={(value) => setData('category_id', value)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Select category" />
                                </SelectTrigger>
                                <SelectContent>
                                    {filteredCategories.map((cat) => (
                                        <SelectItem key={cat.id} value={String(cat.id)}>
                                            {cat.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.category_id && (
                                <p className="text-sm text-destructive">{errors.category_id}</p>
                            )}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <Label>Priority</Label>
                        <Select
                            value={data.priority}
                            onValueChange={(value) => setData('priority', value)}
                        >
                            <SelectTrigger className="w-full sm:w-1/2">
                                <SelectValue placeholder="Select priority" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="low">Low</SelectItem>
                                <SelectItem value="medium">Medium</SelectItem>
                                <SelectItem value="high">High</SelectItem>
                                <SelectItem value="urgent">Urgent</SelectItem>
                            </SelectContent>
                        </Select>
                        {errors.priority && (
                            <p className="text-sm text-destructive">{errors.priority}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="attachments">Attachments</Label>
                        <Input
                            id="attachments"
                            type="file"
                            multiple
                            onChange={(e) => {
                                if (e.target.files) {
                                    setData('attachments', Array.from(e.target.files));
                                }
                            }}
                        />
                        {errors.attachments && (
                            <p className="text-sm text-destructive">{errors.attachments}</p>
                        )}
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Submitting...' : 'Submit Ticket'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
