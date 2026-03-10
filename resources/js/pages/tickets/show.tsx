import { Head, Link, useForm } from '@inertiajs/react';
import { type FormEvent } from 'react';
import { Download, Paperclip } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import type { BreadcrumbItem, Ticket, TicketAttachment, TicketReply } from '@/types';

const statusColors: Record<Ticket['status'], string> = {
    open: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    waiting_reply: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
    resolved: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    closed: 'bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-500',
};

const priorityColors: Record<Ticket['priority'], string> = {
    low: 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
    medium: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    high: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
    urgent: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
};

function formatStatus(status: string): string {
    return status.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
}

function formatFileSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

interface Props {
    ticket: Ticket;
}

function AttachmentList({ attachments }: { attachments: TicketAttachment[] }) {
    if (attachments.length === 0) return null;

    return (
        <div className="mt-3 flex flex-wrap gap-2">
            {attachments.map((attachment) => (
                <a
                    key={attachment.id}
                    href={attachment.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1.5 text-xs text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                >
                    <Download className="size-3" />
                    <span>{attachment.file_name}</span>
                    <span className="text-muted-foreground/70">({formatFileSize(attachment.file_size)})</span>
                </a>
            ))}
        </div>
    );
}

function ReplyItem({ reply }: { reply: TicketReply }) {
    return (
        <div className="rounded-lg border p-4">
            <div className="flex items-center justify-between">
                <span className="text-sm font-medium">{reply.user?.name ?? 'Unknown'}</span>
                <span className="text-xs text-muted-foreground">{formatDate(reply.created_at)}</span>
            </div>
            <div
                className="prose prose-sm dark:prose-invert mt-2 max-w-none"
                dangerouslySetInnerHTML={{ __html: reply.body }}
            />
            {reply.attachments && <AttachmentList attachments={reply.attachments} />}
        </div>
    );
}

export default function TicketsShow({ ticket }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Tickets', href: '/tickets' },
        { title: ticket.reference, href: `/tickets/${ticket.id}` },
    ];

    const { data, setData, post, processing, reset, errors } = useForm<{
        body: string;
        attachments: File[];
    }>({
        body: '',
        attachments: [],
    });

    const isClosedOrResolved = ticket.status === 'closed' || ticket.status === 'resolved';

    function handleReplySubmit(e: FormEvent) {
        e.preventDefault();
        post(`/tickets/${ticket.id}/replies`, {
            forceFormData: true,
            onSuccess: () => reset(),
            preserveScroll: true,
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Ticket ${ticket.reference}`} />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <div className="flex items-center gap-3">
                            <h1 className="text-2xl font-semibold">{ticket.subject}</h1>
                        </div>
                        <p className="mt-1 font-mono text-sm text-muted-foreground">{ticket.reference}</p>
                    </div>
                    <Button variant="outline" asChild>
                        <Link href="/tickets">Back to Tickets</Link>
                    </Button>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2 space-y-6">
                        <div className="rounded-lg border p-4">
                            <h2 className="mb-2 text-sm font-medium text-muted-foreground">Description</h2>
                            <div
                                className="prose prose-sm dark:prose-invert max-w-none"
                                dangerouslySetInnerHTML={{ __html: ticket.description }}
                            />
                            {ticket.attachments && ticket.attachments.length > 0 && (
                                <>
                                    <Separator className="my-4" />
                                    <div className="flex items-center gap-1.5 text-sm text-muted-foreground mb-2">
                                        <Paperclip className="size-3.5" />
                                        Attachments
                                    </div>
                                    <AttachmentList attachments={ticket.attachments} />
                                </>
                            )}
                        </div>

                        <Separator />

                        <div className="space-y-4">
                            <h2 className="text-lg font-semibold">Replies</h2>
                            {(!ticket.replies || ticket.replies.length === 0) && (
                                <p className="text-sm text-muted-foreground">No replies yet.</p>
                            )}
                            {ticket.replies?.map((reply) => (
                                <ReplyItem key={reply.id} reply={reply} />
                            ))}
                        </div>

                        {!isClosedOrResolved && (
                            <>
                                <Separator />
                                <form onSubmit={handleReplySubmit} className="space-y-4">
                                    <h2 className="text-lg font-semibold">Reply</h2>
                                    <div className="space-y-2">
                                        <Label htmlFor="body">Your Message</Label>
                                        <Textarea
                                            id="body"
                                            value={data.body}
                                            onChange={(e) => setData('body', e.target.value)}
                                            placeholder="Type your reply..."
                                            className="min-h-24"
                                            aria-invalid={!!errors.body}
                                        />
                                        {errors.body && (
                                            <p className="text-sm text-destructive">{errors.body}</p>
                                        )}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="reply-attachments">Attachments</Label>
                                        <Input
                                            id="reply-attachments"
                                            type="file"
                                            multiple
                                            onChange={(e) => {
                                                if (e.target.files) {
                                                    setData('attachments', Array.from(e.target.files));
                                                }
                                            }}
                                        />
                                    </div>
                                    <div className="flex justify-end">
                                        <Button type="submit" disabled={processing}>
                                            {processing ? 'Sending...' : 'Send Reply'}
                                        </Button>
                                    </div>
                                </form>
                            </>
                        )}
                    </div>

                    <div className="space-y-4">
                        <div className="rounded-lg border p-4 space-y-4">
                            <h2 className="text-sm font-semibold">Ticket Details</h2>

                            <div className="space-y-3 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Status</span>
                                    <Badge variant="outline" className={statusColors[ticket.status]}>
                                        {formatStatus(ticket.status)}
                                    </Badge>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Priority</span>
                                    <Badge variant="outline" className={priorityColors[ticket.priority]}>
                                        {formatStatus(ticket.priority)}
                                    </Badge>
                                </div>
                                {ticket.department && (
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Department</span>
                                        <span>{ticket.department.name}</span>
                                    </div>
                                )}
                                {ticket.category && (
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Category</span>
                                        <span>{ticket.category.name}</span>
                                    </div>
                                )}
                                {ticket.assigned_agent && (
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Assigned To</span>
                                        <span>{ticket.assigned_agent.name}</span>
                                    </div>
                                )}
                                <Separator />
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Created</span>
                                    <span>{formatDate(ticket.created_at)}</span>
                                </div>
                                {ticket.resolved_at && (
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Resolved</span>
                                        <span>{formatDate(ticket.resolved_at)}</span>
                                    </div>
                                )}
                                {ticket.closed_at && (
                                    <div className="flex justify-between">
                                        <span className="text-muted-foreground">Closed</span>
                                        <span>{formatDate(ticket.closed_at)}</span>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
