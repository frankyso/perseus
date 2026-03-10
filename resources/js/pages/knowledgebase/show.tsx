import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Eye } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import type { BreadcrumbItem, KnowledgebaseArticle, KnowledgebaseCategory } from '@/types';

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
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Knowledgebase', href: '/knowledgebase' },
        { title: category.name, href: `/knowledgebase/${category.slug}` },
        { title: article.title, href: `/knowledgebase/${category.slug}/${article.slug}` },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${article.title} - Knowledgebase`} />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div>
                    <Button variant="ghost" size="sm" asChild className="mb-4">
                        <Link href={`/knowledgebase/${category.slug}`}>
                            <ArrowLeft />
                            Back to {category.name}
                        </Link>
                    </Button>

                    <h1 className="text-3xl font-semibold">{article.title}</h1>

                    <div className="mt-3 flex items-center gap-4 text-sm text-muted-foreground">
                        {article.author && <span>By {article.author.name}</span>}
                        {article.published_at && <span>{formatDate(article.published_at)}</span>}
                        <span className="inline-flex items-center gap-1">
                            <Eye className="size-3.5" />
                            {article.views_count} view{article.views_count !== 1 ? 's' : ''}
                        </span>
                    </div>
                </div>

                <Separator />

                <article
                    className="prose prose-sm dark:prose-invert max-w-none lg:prose-base"
                    dangerouslySetInnerHTML={{ __html: article.body }}
                />
            </div>
        </AppLayout>
    );
}
