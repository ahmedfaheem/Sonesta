import { computed } from 'vue';

export function usePasswordStrength(password) {
    const score = computed(() => {
        const value = password.value || '';
        let total = 0;

        if (value.length >= 6) total += 1;
        if (value.length >= 10) total += 1;
        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) total += 1;
        if (/\d/.test(value)) total += 1;
        if (/[^A-Za-z0-9]/.test(value)) total += 1;

        return total;
    });

    const label = computed(() => {
        if (!password.value) return 'Empty';
        if (score.value <= 2) return 'Weak';
        if (score.value <= 4) return 'Medium';
        return 'Strong';
    });

    const percentage = computed(() => Math.min((score.value / 5) * 100, 100));

    const tone = computed(() => {
        if (!password.value) return 'bg-slate-200';
        if (score.value <= 2) return 'bg-red-500';
        if (score.value <= 4) return 'bg-amber-500';
        return 'bg-emerald-500';
    });

    return {
        score,
        label,
        percentage,
        tone,
    };
}
