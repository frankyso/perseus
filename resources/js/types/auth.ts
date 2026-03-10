export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};

export type Department = {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
};

export type TicketCategory = {
    id: number;
    name: string;
    description: string | null;
    department_id: number | null;
    is_active: boolean;
};

export type Ticket = {
    id: number;
    reference: string;
    subject: string;
    description: string;
    status: 'open' | 'in_progress' | 'waiting_reply' | 'resolved' | 'closed';
    priority: 'low' | 'medium' | 'high' | 'urgent';
    user_id: number;
    department_id: number | null;
    category_id: number | null;
    assigned_to: number | null;
    resolved_at: string | null;
    closed_at: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
    department?: Department;
    category?: TicketCategory;
    assigned_agent?: User;
    replies?: TicketReply[];
    attachments?: TicketAttachment[];
};

export type TicketReply = {
    id: number;
    body: string;
    ticket_id: number;
    user_id: number;
    is_internal_note: boolean;
    created_at: string;
    user?: User;
    attachments?: TicketAttachment[];
};

export type TicketAttachment = {
    id: number;
    file_name: string;
    file_path: string;
    mime_type: string;
    file_size: number;
    url: string;
};

export type KnowledgebaseCategory = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    icon: string | null;
    is_active: boolean;
    articles_count?: number;
    published_articles_count?: number;
};

export type KnowledgebaseArticle = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    body: string;
    status: 'draft' | 'published';
    views_count: number;
    published_at: string | null;
    created_at: string;
    category?: KnowledgebaseCategory;
    author?: User;
};
