import {
    Bot,
    Database,
    FileSearch,
    LineChart,
    UserCheck,
    Workflow,
} from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const capabilities = [
    {
        icon: Database,
        name: 'AI Knowledge (RAG)',
        description:
            'Mencari jawaban dari basis pengetahuan SOP, regulasi, dan panduan internal.',
    },
    {
        icon: Bot,
        name: 'AI Assistant',
        description:
            'Asisten percakapan yang membantu tugas administratif sehari-hari.',
    },
    {
        icon: FileSearch,
        name: 'Document AI',
        description:
            'Membaca dan mengekstrak informasi penting dari dokumen dan surat.',
    },
    {
        icon: LineChart,
        name: 'Analytics & Insight',
        description:
            'Menyajikan analisis dan rekomendasi dari data program dan teknologi.',
    },
    {
        icon: Workflow,
        name: 'AI Agent',
        description:
            'Menjalankan rangkaian tugas otomatis lintas modul secara mandiri.',
    },
    {
        icon: UserCheck,
        name: 'Human-in-the-Loop',
        description:
            'Keputusan penting tetap melalui verifikasi dan persetujuan manusia.',
    },
];

export default function AiCoreSection() {
    return (
        <section
            id="ai"
            className="relative overflow-hidden bg-dkst-navy py-20 text-white"
        >
            <div
                aria-hidden
                className="pointer-events-none absolute top-0 right-0 size-80 rounded-full bg-dkst-cyan/20 blur-3xl"
            />

            <div className="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <span className="text-xs font-semibold tracking-[0.2em] text-dkst-cyan-light uppercase">
                        DKST AI Core
                    </span>
                    <h2 className="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                        Kecerdasan Buatan di Jantung Platform
                    </h2>
                    <p className="mt-3 text-white/70">
                        Enam kemampuan AI yang bekerja di balik setiap modul
                        untuk mempercepat proses dan pengambilan keputusan.
                    </p>
                </div>

                <div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {capabilities.map(({ icon: Icon, name, description }) => (
                        <Card
                            key={name}
                            className="gap-3 border-white/10 bg-white/5 text-white"
                        >
                            <CardHeader>
                                <span className="flex size-10 items-center justify-center rounded-lg bg-dkst-cyan/20 text-dkst-cyan-light">
                                    <Icon className="size-5" />
                                </span>
                                <CardTitle className="mt-2 text-base text-white">
                                    {name}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-white/70">
                                    {description}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </section>
    );
}
