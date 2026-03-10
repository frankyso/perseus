import { Head, Link, router, usePage } from '@inertiajs/react';
import { type FormEvent, useState } from 'react';
import { ArrowLeft, ChevronRight, FileText, MessageCircle, Search } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import KnowledgebaseLayout from '@/layouts/knowledgebase-layout';
import type { Auth, KnowledgebaseArticle } from '@/types';

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
    const { auth } = usePage<{ auth: Auth }>().props;
    const [searchQuery, setSearchQuery] = useState(query);

    function handleSearch(e: FormEvent) {
        e.preventDefault();
        router.get('/knowledgebase/search', { query: searchQuery }, { preserveState: true });
    }

    const hasResults = articles.data.length > 0;

    return (
        <KnowledgebaseLayout>
            <Head title={`Search: ${query} - Help Center`} />

            <div className="mx-auto max-w-3xl px-4 py-8 sm:px-6">
                {/* Breadcrumb */}
                <nav className="mb-6 flex items-center gap-1.5 text-sm text-muted-foreground">
                    <Link href="/knowledgebase" className="hover:text-foreground">
                        Help Center
                    </Link>
                    <ChevronRight className="size-3.5" />
                    <span className="text-foreground">Search</span>
                </nav>

                {/* Search Form */}
                <form onSubmit={handleSearch} className="mb-8">
                    <div className="relative">
                        <Search className="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Search for articles..."
                            className="h-12 rounded-full pl-12 pr-4 text-base shadow-sm"
                            autoFocus
                        />
                    </div>
                </form>

                {/* Results count */}
                {query && (
                    <p className="mb-6 text-sm text-muted-foreground">
                        {hasResults
                            ? `${articles.data.length} result${articles.data.length !== 1 ? 's' : ''} for "${query}"`
                            : `No results for "${query}"`}
                    </p>
                )}

                {/* Results List */}
                {hasResults ? (
                    <div className="divide-y rounded-xl border">
                        {articles.data.map((article) => (
                            <Link
                                key={article.id}
                                href={
                                    article.category
                                        ? `/knowledgebase/${article.category.slug}/${article.slug}`
                                        : '#'
                                }
                                className="flex items-center gap-3 px-5 py-4 transition-colors hover:bg-muted/50"
                            >
                                <FileText className="size-5 shrink-0 text-muted-foreground" />
                                <div className="min-w-0 flex-1">
                                    <h2 className="font-medium">{article.title}</h2>
                                    {article.excerpt && (
                                        <p className="mt-0.5 text-sm text-muted-foreground line-clamp-1">
                                            {article.excerpt}
                                        </p>
                                    )}
                                    <div className="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
                                        {article.category && (
                                            <span className="rounded-full bg-muted px-2 py-0.5">
                                                {article.category.name}
                                            </span>
                                        )}
                                        {article.published_at && (
                                            <span>{formatDate(article.published_at)}</span>
                                        )}
                                    </div>
                                </div>
                                <ChevronRight className="size-4 shrink-0 text-muted-foreground" />
                            </Link>
                        ))}
                    </div>
                ) : query ? (
                    /* No results - CTA to create ticket */
                    <div className="rounded-xl border-2 border-dashed border-primary/20 py-12 text-center">
                        <MessageCircle className="mx-auto size-10 text-primary" />
                        <h3 className="mt-4 text-lg font-semibold">
                            Couldn&apos;t find what you need?
                        </h3>
                        <p className="mx-auto mt-2 max-w-sm text-sm text-muted-foreground">
                            Our support team is ready to help. Submit a request and we&apos;ll get back to you as soon as possible.
                        </p>
                        <Button className="mt-4" asChild>
                            <Link href={auth?.user ? '/tickets/create' : '/login'}>
                                {auth?.user ? 'Submit a Request' : 'Sign in to submit a request'}
                            </Link>
                        </Button>
                    </div>
                ) : null}

                {/* Pagination */}
                {articles.links.length > 3 && (
                    <div className="mt-6 flex justify-center gap-1">
                        {articles.links.map((link, index) => (
                            <Link
                                key={index}
                                href={link.url ?? '#'}
                                className={`rounded px-3 py-1 text-sm ${
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : link.url
                                          ? 'text-muted-foreground hover:bg-muted'
                                          : 'pointer-events-none text-muted-foreground/50'
                                }`}
                                preserveState
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}

                {/* Back */}
                <div className="mt-8">
                    <Link
                        href="/knowledgebase"
                        className="inline-flex items-center text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft className="mr-1.5 size-4" />
                        Back to Help Center
                    </Link>
                </div>
            </div>
        </KnowledgebaseLayout>
    );
}
