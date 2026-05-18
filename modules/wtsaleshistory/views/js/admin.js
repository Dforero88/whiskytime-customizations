(function () {
  function parsePayload(root) {
    try {
      return JSON.parse(root.dataset.chart || '{}');
    } catch (error) {
      return null;
    }
  }

  function formatMoney(value, currencySign) {
    return currencySign + ' ' + value.toFixed(2);
  }

  function getColor(index) {
    var palette = [
      '#0b7285',
      '#b35c1e',
      '#6c3fc6',
      '#198754',
      '#cc3a6d',
      '#f59f00',
      '#364fc7',
      '#0f766e',
      '#9c36b5',
      '#5f3dc4'
    ];

    return palette[index % palette.length];
  }

  function createSvgNode(tag, attrs) {
    var node = document.createElementNS('http://www.w3.org/2000/svg', tag);
    Object.keys(attrs).forEach(function (key) {
      node.setAttribute(key, attrs[key]);
    });
    return node;
  }

  function computeMax(series, key) {
    var max = 0;
    series.forEach(function (yearSeries) {
      yearSeries[key].forEach(function (value) {
        if (value > max) {
          max = value;
        }
      });
    });
    return max;
  }

  function renderChart(root) {
    var payload = parsePayload(root);
    if (!payload || !payload.series || !payload.series.length) {
      return;
    }

    var svg = root.querySelector('.wtsh-svg');
    var legend = root.querySelector('.wtsh-legend');
    var toggles = Array.prototype.slice.call(document.querySelectorAll('.js-wtsh-year-toggle'));
    var width = 1200;
    var height = 520;
    var margin = { top: 20, right: 90, bottom: 70, left: 90 };
    var innerWidth = width - margin.left - margin.right;
    var innerHeight = height - margin.top - margin.bottom;
    var orderMax = computeMax(payload.series, 'orders') || 1;
    var salesMax = computeMax(payload.series, 'sales') || 1;
    var currencySign = payload.currencySign || 'CHF';

    function selectedYears() {
      return toggles.filter(function (toggle) {
        return toggle.checked;
      }).map(function (toggle) {
        return parseInt(toggle.value, 10);
      });
    }

    function draw() {
      var enabledYears = selectedYears();
      var activeSeries = payload.series.filter(function (item) {
        return enabledYears.indexOf(item.year) !== -1;
      });

      svg.innerHTML = '';
      legend.innerHTML = '';

      var group = createSvgNode('g', { transform: 'translate(' + margin.left + ',' + margin.top + ')' });
      svg.appendChild(group);

      for (var i = 0; i <= 4; i++) {
        var y = (innerHeight / 4) * i;
        group.appendChild(createSvgNode('line', {
          x1: 0,
          y1: y,
          x2: innerWidth,
          y2: y,
          stroke: '#e9eef4',
          'stroke-width': 1
        }));
      }

      payload.months.forEach(function (month, index) {
        var x = (innerWidth / 11) * index;
        group.appendChild(createSvgNode('line', {
          x1: x,
          y1: 0,
          x2: x,
          y2: innerHeight,
          stroke: '#f3f6f9',
          'stroke-width': 1
        }));

        var label = createSvgNode('text', {
          x: x,
          y: innerHeight + 28,
          'text-anchor': 'middle',
          fill: '#6c7a89',
          'font-size': '12'
        });
        label.textContent = month;
        group.appendChild(label);
      });

      for (var tick = 0; tick <= 4; tick++) {
        var orderValue = Math.round((orderMax / 4) * (4 - tick));
        var salesValue = (salesMax / 4) * (4 - tick);
        var yTick = (innerHeight / 4) * tick;

        var leftLabel = createSvgNode('text', {
          x: -12,
          y: yTick + 4,
          'text-anchor': 'end',
          fill: '#6c7a89',
          'font-size': '12'
        });
        leftLabel.textContent = orderValue;
        group.appendChild(leftLabel);

        var rightLabel = createSvgNode('text', {
          x: innerWidth + 12,
          y: yTick + 4,
          'text-anchor': 'start',
          fill: '#6c7a89',
          'font-size': '12'
        });
        rightLabel.textContent = formatMoney(salesValue, currencySign);
        group.appendChild(rightLabel);
      }

      activeSeries.forEach(function (series, index) {
        var color = getColor(index);
        var ordersPoints = [];
        var salesPoints = [];

        series.orders.forEach(function (value, monthIndex) {
          var x = (innerWidth / 11) * monthIndex;
          var y = innerHeight - (value / orderMax) * innerHeight;
          ordersPoints.push(x + ',' + y);
        });

        series.sales.forEach(function (value, monthIndex) {
          var x = (innerWidth / 11) * monthIndex;
          var y = innerHeight - (value / salesMax) * innerHeight;
          salesPoints.push(x + ',' + y);
        });

        group.appendChild(createSvgNode('polyline', {
          points: ordersPoints.join(' '),
          fill: 'none',
          stroke: color,
          'stroke-width': 3,
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round'
        }));

        group.appendChild(createSvgNode('polyline', {
          points: salesPoints.join(' '),
          fill: 'none',
          stroke: color,
          'stroke-width': 2,
          'stroke-dasharray': '8 6',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          opacity: 0.9
        }));

        var legendItem = document.createElement('div');
        legendItem.className = 'wtsh-legend-item';
        legendItem.innerHTML =
          '<span class="wtsh-legend-color" style="background:' + color + '"></span>' +
          '<strong>' + series.year + '</strong>';
        legend.appendChild(legendItem);
      });
    }

    toggles.forEach(function (toggle) {
      toggle.addEventListener('change', draw);
    });

    draw();
  }

  document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('wtsh-chart-root');
    if (root) {
      renderChart(root);
    }
  });
})();
