/**
 * dashboard.js - Chart.js Dashboard Charts
 * Hiển thị biểu đồ tiến độ học tập (Light Theme)
 */

function initDashboardCharts(data) {
    if (!data || !data.topics || data.topics.length === 0) return;

    // Light theme config
    Chart.defaults.color = '#64748B';
    Chart.defaults.borderColor = '#E2E8F0';

    const tooltipConfig = {
        backgroundColor: '#FFFFFF',
        titleColor: '#1E293B',
        bodyColor: '#64748B',
        borderColor: '#E2E8F0',
        borderWidth: 1,
        padding: 12,
        cornerRadius: 8,
        boxShadow: '0 4px 12px rgba(0,0,0,0.1)',
    };

    // === Bar Chart: Điểm theo chủ đề ===
    const scoreCtx = document.getElementById('topicScoreChart');
    if (scoreCtx) {
        new Chart(scoreCtx, {
            type: 'bar',
            data: {
                labels: data.topics,
                datasets: [{
                    label: 'Tổng điểm',
                    data: data.scores,
                    backgroundColor: [
                        'rgba(91, 108, 255, 0.7)',
                        'rgba(244, 63, 94, 0.7)',
                        'rgba(6, 182, 212, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                    ],
                    borderColor: ['#5B6CFF','#F43F5E','#06B6D4','#10B981','#F59E0B','#8B5CF6'],
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: tooltipConfig },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { color: '#94A3B8' } },
                    x: { grid: { display: false }, ticks: { color: '#64748B' } }
                }
            }
        });
    }

    // === Radar Chart: Phân bố kỹ năng ===
    const radarCtx = document.getElementById('skillRadarChart');
    if (radarCtx && data.overall) {
        const maxVal = Math.max(
            data.overall.total_vocab_learned || 1,
            data.overall.total_lessons_completed || 1,
            data.overall.total_tests_passed || 1,
            data.overall.total_speaking_practiced || 1
        );
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: ['Từ vựng', 'Bài học', 'Bài test', 'Speaking'],
                datasets: [{
                    label: 'Kỹ năng',
                    data: [
                        data.overall.total_vocab_learned || 0,
                        data.overall.total_lessons_completed || 0,
                        data.overall.total_tests_passed || 0,
                        data.overall.total_speaking_practiced || 0
                    ],
                    backgroundColor: 'rgba(91, 108, 255, 0.15)',
                    borderColor: '#5B6CFF',
                    borderWidth: 2,
                    pointBackgroundColor: '#5B6CFF',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false }, tooltip: tooltipConfig },
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0' },
                        angleLines: { color: '#E2E8F0' },
                        pointLabels: { color: '#1E293B', font: { size: 12, weight: 600 } },
                        ticks: { display: false }
                    }
                }
            }
        });
    }
}
