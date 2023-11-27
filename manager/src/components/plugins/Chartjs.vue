<script>
import Chart from 'chart.js/auto';
import { useAppVariableStore } from '@/stores/app-variable';

const appVariable = useAppVariableStore();

export default {
	props: ['data', 'type', 'options', 'width', 'height'],
  mounted() {
  	Chart.defaults.font.family = appVariable.font.family;
  	Chart.defaults.font.weight = 600;
		Chart.defaults.color = 'rgba('+ appVariable.color.componentColorRgb +', .65)';
		Chart.defaults.plugins.legend.display = false;
		Chart.defaults.plugins.tooltip.padding = 8;
		Chart.defaults.plugins.tooltip.backgroundColor = 'rgba('+ appVariable.color.gray800Rgb +', .95)';
		Chart.defaults.plugins.tooltip.titleFont.family = appVariable.font.family;
		Chart.defaults.plugins.tooltip.titleFont.weight = 600;
		Chart.defaults.plugins.tooltip.footerFont.family = appVariable.font.family;
		Chart.defaults.scale.grid.borderColor = 'rgba('+ appVariable.color.componentColorRgb +', .15)';
		
		new Chart(this.$refs.canvas, {
			type: this.$props.type,
			data: this.$props.data,
			options: this.$props.options
		});
  }
}
</script>

<template>
	<canvas ref="canvas" :height="height" />
</template>