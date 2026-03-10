import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem, Ticket } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tickets', href: '/tickets' },
];

type StatusTab = 'all' | 'open' | 'in_progress' | 'resolved' | 'closed';

const statusTabs: { label: string; value: StatusTab }[] = [
    { label: 'All', value: 'all' },
    { label: 'Open', value: 'open' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Resolved', value: 'resolved' },
    { label: 'Closed', value: 'closed' },
];

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
    });
}

interface Props {
    tickets: {
        data: Ticket[];
        links: { url: string | null; label: string; active: boolean }[];
        meta?: { current_page: number; last_page: number };
    };
    filters: {
        status?: string;
        priority?: string;
    };
}

export default function TicketsIndex({ tickets, filters }: Props) {
    const currentStatus = (filters.status || 'all') as StatusTab;

    function handleStatusFilter(status: StatusTab) {
        router.get(
            '/tickets',
            status === 'all' ? {} : { status },
            { preserveState: true, preserveScroll: true },
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="My Tickets" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-semibold">My Tickets</h1>
                    <Button asChild>
                        <Link href="/tickets/create">
                            <Plus />
                            New Ticket
                        </Link>
                    </Button>
                </div>

                <div className="flex gap-1 border-b">
                    {statusTabs.map((tab) => (
                        <button
                            key={tab.value}
                            onClick={() => handleStatusFilter(tab.value)}
                            className={`px-4 py-2 text-sm font-medium transition-colors ${
                                currentStatus === tab.value
                                    ? 'border-b-2 border-primary text-primary'
                                    : 'text-muted-foreground hover:text-foreground'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>

                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b bg-muted/50">
                                <th className="px-4 py-3 text-left font-medium">Reference</th>
                                <th className="px-4 py-3 text-left font-medium">Subject</th>
                                <th className="px-4 py-3 text-left font-medium">Status</th>
                                <th className="px-4 py-3 text-left font-medium">Priority</th>
                                <th className="px-4 py-3 text-left font-medium">Department</th>
                                <th className="px-4 py-3 text-left font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            {tickets.data.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                        No tickets found.
                                    </td>
                                </tr>
                            )}
                            {tickets.data.map((ticket) => (
                                <tr key={ticket.id} className="border-b last:border-0 hover:bg-muted/30 transition-colors">
                                    <td className="px-4 py-3">
                                        <Link
                                            href={`/tickets/${ticket.id}`}
                                            className="font-mono text-xs text-primary hover:underline"
                                        >
                                            {ticket.reference}
                                        </Link>
                                    </td>
                                    <td className="px-4 py-3">
                                        <Link
                                            href={`/tickets/${ticket.id}`}
                                            className="font-medium hover:underline"
                                        >
                                            {ticket.subject}
                                        </Link>
                                    </td>
                                    <td className="px-4 py-3">
                                        <Badge variant="outline" className={statusColors[ticket.status]}>
                                            {formatStatus(ticket.status)}
                                        </Badge>
                                    </td>
                                    <td className="px-4 py-3">
                                        <Badge variant="outline" className={priorityColors[ticket.priority]}>
                                            {formatStatus(ticket.priority)}
                                        </Badge>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {ticket.department?.name ?? '-'}
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {formatDate(ticket.created_at)}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {tickets.links.length > 3 && (
                    <div className="flex justify-center gap-1">
                        {tickets.links.map((link, index) => (
                            <Link
                                key={index}
                                href={link.url ?? '#'}
                                className={`px-3 py-1 rounded text-sm ${
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : link.url
                                          ? 'text-muted-foreground hover:bg-muted'
                                          : 'text-muted-foreground/50 pointer-events-none'
                                }`}
                                preserveState
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
