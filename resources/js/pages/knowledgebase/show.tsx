import { Head, Link, usePage } from '@inertiajs/react';
import { ChevronRight, Eye, MessageCircle, ThumbsDown, ThumbsUp } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import KnowledgebaseLayout from '@/layouts/knowledgebase-layout';
import type { Auth, KnowledgebaseArticle, KnowledgebaseCategory } from '@/types';

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
}

interface Props {
    category: KnowledgebaseCategory;
    article: KnowledgebaseArticle;
}

export default function KnowledgebaseShow({ category, article }: Props) {
    const { auth } = usePage<{ auth: Auth }>().props;
    const [feedback, setFeedback] = useState<'helpful' | 'not_helpful' | null>(null);

    return (
        <KnowledgebaseLayout>
            <Head title={`${article.title} - Help Center`} />

            <div className="mx-auto max-w-3xl px-4 py-8 sm:px-6">
                {/* Breadcrumb */}
                <nav className="mb-8 flex items-center gap-1.5 text-sm text-muted-foreground">
                    <Link href="/knowledgebase" className="hover:text-foreground">
                        Help Center
                    </Link>
                    <ChevronRight className="size-3.5" />
                    <Link href={`/knowledgebase/${category.slug}`} className="hover:text-foreground">
                        {category.name}
                    </Link>
                    <ChevronRight className="size-3.5" />
                    <span className="line-clamp-1 text-foreground">{article.title}</span>
                </nav>

                {/* Article Header */}
                <header className="mb-8">
                    <h1 className="text-3xl font-bold leading-tight">{article.title}</h1>
                    <div className="mt-3 flex flex-wrap items-center gap-3 text-sm text-muted-foreground">
                        {article.author && <span>Written by {article.author.name}</span>}
                        {article.published_at && (
                            <>
                                <span>&middot;</span>
                                <span>Updated {formatDate(article.published_at)}</span>
                            </>
                        )}
                        <span>&middot;</span>
                        <span className="inline-flex items-center gap-1">
                            <Eye className="size-3.5" />
                            {article.views_count}
                        </span>
                    </div>
                </header>

                <Separator />

                {/* Article Body */}
                <article
                    className="prose prose-neutral dark:prose-invert mt-8 max-w-none prose-headings:font-semibold prose-a:text-primary prose-img:rounded-lg"
                    dangerouslySetInnerHTML={{ __html: article.body }}
                />

                {/* Feedback Section */}
                <div className="mt-12 rounded-xl border bg-muted/30 p-6 text-center">
                    {feedback === null ? (
                        <>
                            <p className="text-sm font-medium">Was this article helpful?</p>
                            <div className="mt-3 flex justify-center gap-3">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => setFeedback('helpful')}
                                >
                                    <ThumbsUp className="mr-1.5 size-4" />
                                    Yes
                                </Button>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => setFeedback('not_helpful')}
                                >
                                    <ThumbsDown className="mr-1.5 size-4" />
                                    No
                                </Button>
                            </div>
                        </>
                    ) : feedback === 'helpful' ? (
                        <p className="text-sm text-muted-foreground">
                            Great! Glad this article helped. 🎉
                        </p>
                    ) : (
                        <div>
                            <p className="text-sm text-muted-foreground">
                                Sorry this didn&apos;t help. Let us assist you directly.
                            </p>
                            <Button size="sm" className="mt-3" asChild>
                                <Link href="/tickets/create">
                                    <MessageCircle className="mr-1.5 size-4" />
                                    Submit a Request
                                </Link>
                            </Button>
                        </div>
                    )}
                </div>

                {/* Still need help CTA */}
                <div className="mt-8 rounded-xl border-2 border-dashed border-primary/20 p-8 text-center">
                    <MessageCircle className="mx-auto size-8 text-primary" />
                    <h3 className="mt-3 text-lg font-semibold">Still need help?</h3>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Can&apos;t find what you&apos;re looking for? Our support team is here to help.
                    </p>
                    <Button className="mt-4" asChild>
                        <Link href={auth?.user ? '/tickets/create' : '/login'}>
                            {auth?.user ? 'Submit a Request' : 'Sign in to submit a request'}
                        </Link>
                    </Button>
                </div>
            </div>
        </KnowledgebaseLayout>
    );
}
