const relativeTimeFormatter = new Intl.RelativeTimeFormat('id', {
    numeric: 'auto',
});

const RELATIVE_TIME_DIVISIONS: [Intl.RelativeTimeFormatUnit, number][] = [
    ['year', 60 * 60 * 24 * 365],
    ['month', 60 * 60 * 24 * 30],
    ['week', 60 * 60 * 24 * 7],
    ['day', 60 * 60 * 24],
    ['hour', 60 * 60],
    ['minute', 60],
];

/** Format an ISO date string as Indonesian relative time, e.g. "12 menit yang lalu". */
export function formatRelativeTime(isoDate: string): string {
    const diffSeconds = Math.round(
        (new Date(isoDate).getTime() - Date.now()) / 1000,
    );

    for (const [unit, secondsInUnit] of RELATIVE_TIME_DIVISIONS) {
        if (Math.abs(diffSeconds) >= secondsInUnit) {
            return relativeTimeFormatter.format(
                Math.round(diffSeconds / secondsInUnit),
                unit,
            );
        }
    }

    return relativeTimeFormatter.format(diffSeconds, 'second');
}

/** Format a date as a long Indonesian date, e.g. "Kamis, 1 Oktober 2026". */
export function formatIndonesianDate(date: Date = new Date()): string {
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

/** Format a number with Indonesian thousands separators, e.g. "12.345". */
export function formatNumber(value: number): string {
    return new Intl.NumberFormat('id-ID').format(value);
}

/** Format a number as Indonesian Rupiah, e.g. "Rp12.345.000". */
export function formatRupiah(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
}
