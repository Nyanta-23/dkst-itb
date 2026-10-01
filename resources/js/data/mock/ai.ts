// TODO: ganti dengan endpoint backend POST /ai/chat

export type AiChatMessage = {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    sources?: string[];
};

export const suggestedQuestions: string[] = [
    'Bagaimana status program tahun ini?',
    'Apa saja teknologi yang siap dilisensikan?',
    'Berapa booking ruangan yang menunggu persetujuan?',
    'Jelaskan SOP pengajuan lisensi teknologi.',
];

export function getMockAiReply(question: string): {
    content: string;
    sources: string[];
} {
    return {
        content: `Ini jawaban contoh untuk pertanyaan "${question}". Jawaban asli nantinya akan diambil dari AI Knowledge Base DKST berdasarkan dokumen dan data yang relevan.`,
        sources: ['SOP Lisensi Teknologi', 'Panduan Peminjaman Ruangan'],
    };
}
