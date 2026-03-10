import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Moon, Sun, Ticket } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';
import type { Auth } from '@/types/auth';

export default function KnowledgebaseLayout({ children }: { children: React.ReactNode }) {
    const { auth } = usePage<{ auth: Auth }>().props;
    const { resolvedAppearance, updateAppearance } = useAppearance();

    function toggleTheme() {
        updateAppearance(resolvedAppearance === 'dark' ? 'light' : 'dark');
    }

    return (
        <div className="min-h-screen bg-background">
            <header className="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                <div className="mx-auto flex h-14 max-w-5xl items-center justify-between px-4 sm:px-6">
                    <Link href="/knowledgebase" className="flex items-center gap-2">
                        <div className="flex size-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
                            <AppLogoIcon className="size-5 fill-current" />
                        </div>
                        <span className="font-semibold">Help Center</span>
                    </Link>

                    <div className="flex items-center gap-2">
                        <Button
                            variant="ghost"
                            size="icon"
                            className="size-8"
                            onClick={toggleTheme}
                            aria-label="Toggle dark mode"
                        >
                            {resolvedAppearance === 'dark' ? (
                                <Sun className="size-4" />
                            ) : (
                                <Moon className="size-4" />
                            )}
                        </Button>
                        {auth?.user ? (
                            <>
                                <Button variant="ghost" size="sm" asChild>
                                    <Link href="/tickets">
                                        <Ticket className="mr-1.5 size-4" />
                                        My Tickets
                                    </Link>
                                </Button>
                                <Button size="sm" asChild>
                                    <Link href="/tickets/create">Submit a Request</Link>
                                </Button>
                            </>
                        ) : (
                            <Button size="sm" asChild>
                                <Link href="/login">Sign In</Link>
                            </Button>
                        )}
                    </div>
                </div>
            </header>

            <main>{children}</main>

            <footer className="border-t">
                <div className="mx-auto flex max-w-5xl flex-col items-center gap-4 px-4 py-8 text-center text-sm text-muted-foreground sm:px-6">
                    <div className="flex items-center gap-4">
                        <Link href="/knowledgebase" className="inline-flex items-center gap-1 hover:text-foreground">
                            <BookOpen className="size-3.5" />
                            Help Center
                        </Link>
                        {auth?.user && (
                            <Link href="/tickets" className="inline-flex items-center gap-1 hover:text-foreground">
                                <Ticket className="size-3.5" />
                                My Tickets
                            </Link>
                        )}
                    </div>
                    <p>&copy; {new Date().getFullYear()} Perseus. All rights reserved.</p>
                </div>
            </footer>
        </div>
    );
}
