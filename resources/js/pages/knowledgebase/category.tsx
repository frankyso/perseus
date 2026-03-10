import { Head, Link, router } from '@inertiajs/react';
import { type FormEvent, useState } from 'react';
import { ArrowLeft, ChevronRight, FileText, Search } from 'lucide-react';
import { Input } from '@/components/ui/input';
import KnowledgebaseLayout from '@/layouts/knowledgebase-layout';
import type { KnowledgebaseArticle, KnowledgebaseCategory } from '@/types';

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

interface Props {
    category: KnowledgebaseCategory;
    articles: {
        data: KnowledgebaseArticle[];
        links: { url: string | null; label: string; active: boolean }[];
    };
}

export default function KnowledgebaseCategoryPage({ category, articles }: Props) {
    const [searchQuery, setSearchQuery] = useState('');

    function handleSearch(e: FormEvent) {
        e.preventDefault();
        if (searchQuery.trim()) {
            router.get('/knowledgebase/search', { query: searchQuery });
        }
    }

    return (
        <KnowledgebaseLayout>
            <Head title={`${category.name} - Help Center`} />

            <div className="mx-auto max-w-3xl px-4 py-8 sm:px-6">
                {/* Breadcrumb */}
                <nav className="mb-6 flex items-center gap-1.5 text-sm text-muted-foreground">
                    <Link href="/knowledgebase" className="hover:text-foreground">
                        Help Center
                    </Link>
                    <ChevronRight className="size-3.5" />
                    <span className="text-foreground">{category.name}</span>
                </nav>

                {/* Search */}
                <form onSubmit={handleSearch} className="mb-8">
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            placeholder="Search articles..."
                            className="pl-10"
                        />
                    </div>
                </form>

                {/* Category Header */}
                <div className="mb-6">
                    <h1 className="text-2xl font-bold">{category.name}</h1>
                    {category.description && (
                        <p className="mt-1 text-muted-foreground">{category.description}</p>
                    )}
                </div>

                {/* Articles List */}
                {articles.data.length === 0 ? (
                    <div className="rounded-xl border bg-muted/30 py-12 text-center">
                        <FileText className="mx-auto size-10 text-muted-foreground/50" />
                        <p className="mt-3 text-muted-foreground">
                            No articles in this category yet.
                        </p>
                        <Link
                            href="/tickets/create"
                            className="mt-2 inline-block text-sm font-medium text-primary hover:underline"
                        >
                            Submit a request instead
                        </Link>
                    </div>
                ) : (
                    <div className="divide-y rounded-xl border">
                        {articles.data.map((article) => (
                            <Link
                                key={article.id}
                                href={`/knowledgebase/${category.slug}/${article.slug}`}
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
                                </div>
                                <ChevronRight className="size-4 shrink-0 text-muted-foreground" />
                            </Link>
                        ))}
                    </div>
                )}

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

                {/* Back link */}
                <div className="mt-8">
                    <Link
                        href="/knowledgebase"
                        className="inline-flex items-center text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft className="mr-1.5 size-4" />
                        Back to all categories
                    </Link>
                </div>
            </div>
        </KnowledgebaseLayout>
    );
}
