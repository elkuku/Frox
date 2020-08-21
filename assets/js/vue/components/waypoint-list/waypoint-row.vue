<template>
    <tr>
        <td>{{waypoint.id}}</td>
        <td>{{waypoint.city}}</td>
        <td>{{waypoint.category}}</td>
        <td>
            {{waypoint.name}}
<!--            <a :href="'/agent/'+agent.id">{{ agent.nickname }}</a>-->
        </td>
        <td>link</td>
        <td>a</td>
<!--        <td>{{ agent.realName }}</td>-->
<!--        <td>-->
<!--            <a :href="'/stats/agent/'+agent.id"><span class="oi oi-graph"></span></a>-->
<!--        </td>-->
    </tr>
</template>

<script>
export default {
    name: 'WaypointRow',
    props: {
        waypoint: {
            type: Object,
            required: true,
        },
    },
}
</script>
