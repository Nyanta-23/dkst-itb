import { Head } from '@inertiajs/react';
import AiCoreSection from '@/components/landing/ai-core-section';
import AudienceSection from '@/components/landing/audience-section';
import BenefitsSection from '@/components/landing/benefits-section';
import CtaSection from '@/components/landing/cta-section';
import HeroSection from '@/components/landing/hero-section';
import ServicesSection from '@/components/landing/services-section';
import SiteFooter from '@/components/landing/site-footer';
import SiteNavbar from '@/components/landing/site-navbar';
import type { LandingStats } from '@/components/landing/stats-section';
import StatsSection from '@/components/landing/stats-section';

export default function Welcome({ stats }: { stats: LandingStats }) {
    return (
        <>
            <Head title="DKST Digital & AI Platform" />

            <div className="min-h-screen bg-background">
                <SiteNavbar />
                <main>
                    <HeroSection />
                    <StatsSection stats={stats} />
                    <ServicesSection />
                    <AiCoreSection />
                    <AudienceSection />
                    <BenefitsSection />
                    <CtaSection />
                </main>
                <SiteFooter />
            </div>
        </>
    );
}
