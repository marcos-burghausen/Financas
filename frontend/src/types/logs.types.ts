// src/types/logs.types.ts

export interface ActivityLog {
  id: number;
  email: string;
  timestamp: string;
  user_agent: string;
  ip: string;
  action: string;
  created_at: string;
  updated_at: string;
}

export interface ActivityLogFilters {
  action?: string;
  email?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
  page?: number;
}

export interface ActivityLogsResponse {
  current_page: number;
  data: ActivityLog[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}
