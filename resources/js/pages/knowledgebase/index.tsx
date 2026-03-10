import { Head, Link, router } from '@inertiajs/react';
import { type FormEvent, useState } from 'react';
import { BookOpen, Search } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { BreadcrumbItem, KnowledgebaseCategory } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Knowledgebase', href: '/knowledgebase' },
];

interface Props {
    categories: KnowledgebaseCategory[];
}

export default function KnowledgebaseIndex({ categories }: Props) {
    const [searchQuery, setSearchQuery] = useState('');

    function handleSearch(e: FormEvent) {
        e.preventDefault();
        if (searchQuery.trim()) {
            router.get('/knowledgebase/search', { query: searchQuery });
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Knowledgebase" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="text-center">
                    <h1 className="text-3xl font-semibold">Knowledgebase</h1>
                    <p className="mt-2 text-muted-foreground">
                        Find answers to common questions and helpful guides.
                    </p>
                </div>

                <form onSubmit={handleSearch} className="mx-auto flex w-full max-w-lg gap-2">
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

                {categories.length === 0 && (
                    <p className="text-center text-muted-foreground">No categories available yet.</p>
                )}

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {categories.map((category) => (
                        <Link key={category.id} href={`/knowledgebase/${category.slug}`}>
                            <Card className="h-full transition-shadow hover:shadow-md">
                                <CardHeader>
                                    <div className="flex items-center gap-2">
                                        <BookOpen className="size-5 text-primary" />
                                        <CardTitle>{category.name}</CardTitle>
                                    </div>
                                    {category.description && (
                                        <CardDescription>{category.description}</CardDescription>
                                    )}
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground">
                                        {category.published_articles_count ?? 0} article{(category.published_articles_count ?? 0) !== 1 ? 's' : ''}
                                    </p>
                                </CardContent>
                            </Card>
                        </Link>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
