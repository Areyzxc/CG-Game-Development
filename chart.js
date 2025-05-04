  // ─────────────────────────────────────────────────────────
// 7️⃣ Progress Chart & Achievements Reveal
// ─────────────────────────────────────────────────────────

// A) Chart.js Donut Chart
const ctx = document.getElementById('completionChart').getContext('2d');
new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Completed', 'Remaining'],
    datasets: [{
      data: [75, 25],           // placeholder percentages
      backgroundColor: [
        getComputedStyle(document.documentElement).getPropertyValue('--chart-color').trim(),
        'rgba(255,255,255,0.1)'
      ],
      borderWidth: 0
    }]
  },
  options: {
    cutout: '70%',
    plugins: { legend: { display: false } },
    animation: { animateRotate: true, duration: 1000 }
  }
});