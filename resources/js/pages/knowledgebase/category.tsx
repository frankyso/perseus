import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem, KnowledgebaseArticle, KnowledgebaseCategory } from '@/types';

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

export default function KnowledgebaseCategory({ category, articles }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Knowledgebase', href: '/knowledgebase' },
        { title: category.name, href: `/knowledgebase/${category.slug}` },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${category.name} - Knowledgebase`} />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div>
                    <Button variant="ghost" size="sm" asChild className="mb-2">
                        <Link href="/knowledgebase">
                            <ArrowLeft />
                            Back to Knowledgebase
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-semibold">{category.name}</h1>
                    {category.description && (
                        <p className="mt-1 text-muted-foreground">{category.description}</p>
                    )}
                </div>

                {articles.data.length === 0 && (
                    <p className="text-muted-foreground">No articles in this category yet.</p>
                )}

                <div className="space-y-3">
                    {articles.data.map((article) => (
                        <Link
                            key={article.id}
                            href={`/knowledgebase/${category.slug}/${article.slug}`}
                            className="block rounded-lg border p-4 transition-colors hover:bg-muted/50"
                        >
                            <h2 className="font-medium">{article.title}</h2>
                            {article.excerpt && (
                                <p className="mt-1 text-sm text-muted-foreground line-clamp-2">
                                    {article.excerpt}
                                </p>
                            )}
                            <div className="mt-2 flex items-center gap-3 text-xs text-muted-foreground">
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
