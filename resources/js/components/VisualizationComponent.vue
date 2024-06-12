// resources/js/components/VisualizationComponent.vue
<template>
    <div>
        <select v-model="selectedKey" @change="fetchData">
            <option value="gpt_table_para_pair_agent">Agent</option>
            <option value="gpt_table_para_pair_noagent">No Agent</option>
        </select>
        <div id="chart"></div>
    </div>
</template>

<script>
import * as vega from 'vega';
import * as vl from 'vega-lite';
import { compile } from 'vega-lite';

export default {
    data() {
        return {
            selectedKey: 'gpt_table_para_pair_agent',
            articleId: '2131', // Change this based on your need
        };
    },
    methods: {
        fetchData() {
            fetch(`/data/${this.articleId}/${this.selectedKey}`)
                .then(response => response.json())
                .then(data => {
                    const spec = JSON.parse(data[0].vegalite_spec);
                    const { spec: compiledSpec } = compile(spec);
                    const runtime = vega.parse(compiledSpec);
                    new vega.View(runtime)
                        .renderer('canvas')
                        .initialize('#chart')
                        .run();
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    },
    mounted() {
        this.fetchData();
    }
};
</script>

<style>
#chart {
    width: 100%;
    height: 500px;
}
</style>
