import { Head, Link, router } from '@inertiajs/react';
import { type FormEvent, useState } from 'react';
import { ArrowRight, BookOpen, FileText, Search } from 'lucide-react';
import { Input } from '@/components/ui/input';
import KnowledgebaseLayout from '@/layouts/knowledgebase-layout';
import type { KnowledgebaseCategory } from '@/types';

interface Props {
    categories: KnowledgebaseCategory[];
}

const categoryIcons: Record<string, React.ReactNode> = {
    'Getting Started': <BookOpen className="size-6" />,
    'Account & Billing': <FileText className="size-6" />,
};

export default function KnowledgebaseIndex({ categories }: Props) {
    const [searchQuery, setSearchQuery] = useState('');

    function handleSearch(e: FormEvent) {
        e.preventDefault();
        if (searchQuery.trim()) {
            router.get('/knowledgebase/search', { query: searchQuery });
        }
    }

    return (
        <KnowledgebaseLayout>
            <Head title="Help Center" />

            {/* Hero Section */}
            <div className="bg-gradient-to-b from-muted/50 to-background">
                <div className="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6 sm:py-24">
                    <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">
                        How can we help?
                    </h1>
                    <p className="mx-auto mt-4 max-w-xl text-lg text-muted-foreground">
                        Search our knowledge base for answers or browse categories below.
                    </p>

                    <form onSubmit={handleSearch} className="mx-auto mt-8 max-w-xl">
                        <div className="relative">
                            <Search className="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Search for articles..."
                                className="h-12 rounded-full pl-12 pr-4 text-base shadow-sm"
                            />
                        </div>
                    </form>
                </div>
            </div>

            {/* Categories Grid */}
            <div className="mx-auto max-w-5xl px-4 pb-16 sm:px-6">
                {categories.length === 0 ? (
                    <p className="py-12 text-center text-muted-foreground">
                        No categories available yet. Check back soon!
                    </p>
                ) : (
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {categories.map((category) => (
                            <Link
                                key={category.id}
                                href={`/knowledgebase/${category.slug}`}
                                className="group rounded-xl border bg-card p-6 transition-all hover:border-primary/20 hover:shadow-md"
                            >
                                <div className="mb-3 inline-flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    {categoryIcons[category.name] ?? <BookOpen className="size-5" />}
                                </div>
                                <h2 className="text-lg font-semibold">{category.name}</h2>
                                {category.description && (
                                    <p className="mt-1 text-sm text-muted-foreground line-clamp-2">
                                        {category.description}
                                    </p>
                                )}
                                <div className="mt-4 flex items-center text-sm font-medium text-primary opacity-0 transition-opacity group-hover:opacity-100">
                                    {category.published_articles_count ?? 0} article{(category.published_articles_count ?? 0) !== 1 ? 's' : ''}
                                    <ArrowRight className="ml-1 size-4" />
                                </div>
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        </KnowledgebaseLayout>
    );
}
