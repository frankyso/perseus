import { Head, Link, router } from '@inertiajs/react';
import { type FormEvent, useState } from 'react';
import { ArrowLeft, Search } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { BreadcrumbItem, KnowledgebaseArticle } from '@/types';

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

interface Props {
    articles: {
        data: KnowledgebaseArticle[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    query: string;
}

export default function KnowledgebaseSearch({ articles, query }: Props) {
    const [searchQuery, setSearchQuery] = useState(query);

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Knowledgebase', href: '/knowledgebase' },
        { title: 'Search', href: '/knowledgebase/search' },
    ];

    function handleSearch(e: FormEvent) {
        e.preventDefault();
        router.get('/knowledgebase/search', { query: searchQuery }, { preserveState: true });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Search: ${query} - Knowledgebase`} />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div>
                    <Button variant="ghost" size="sm" asChild className="mb-2">
                        <Link href="/knowledgebase">
                            <ArrowLeft />
                            Back to Knowledgebase
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-semibold">Search Results</h1>
                </div>

                <form onSubmit={handleSearch} className="flex w-full max-w-lg gap-2">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Search articles..."
                            className="pl-9"
                        />
                    </div>
                    <Button type="submit">Search</Button>
                </form>

                {query && articles.data.length === 0 && (
                    <p className="text-muted-foreground">
                        No results found for &ldquo;{query}&rdquo;. Try a different search term.
                    </p>
                )}

                <div className="space-y-3">
                    {articles.data.map((article) => (
                        <Link
                            key={article.id}
                            href={
                                article.category
                                    ? `/knowledgebase/${article.category.slug}/${article.slug}`
                                    : '#'
                            }
                            className="block rounded-lg border p-4 transition-colors hover:bg-muted/50"
                        >
                            <h2 className="font-medium">{article.title}</h2>
                            {article.excerpt && (
                                <p className="mt-1 text-sm text-muted-foreground line-clamp-2">
                                    {article.excerpt}
                                </p>
                            )}
                            <div className="mt-2 flex items-center gap-3 text-xs text-muted-foreground">
                                {article.category && (
                                    <span className="rounded bg-muted px-1.5 py-0.5">
                                        {article.category.name}
                                    </span>
                                )}
                                {article.author && <span>By {article.author.name}</span>}
                                {article.published_at && <span>{formatDate(article.published_at)}</span>}
                            </div>
                        </Link>
                    ))}
                </div>

                {articles.links.length > 3 && (
                    <div className="flex justify-center gap-1">
                        {articles.links.map((link, index) => (
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
