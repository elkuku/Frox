<template>
    <div>
        <loading v-show="loading"/>
        <h5
            v-show="!loading && waypoints.length === 0"
            class="text-warning"
        >
            No results found
<!--            {{translate('no.search.result.criteria')}}-->
        </h5>

        <table class="table table-sm table-hover">
            <thead>
            <tr>
                <th>Id</th>
                <th>City</th>
                <th>Category</th>
                <th>Name</th>
                <th>Link</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <waypoint-row
                v-show="!loading"
                v-for="waypoint in waypoints"
                :key="waypoint['@id']"
                :waypoint="waypoint"
            />
            </tbody>
        </table>
    </div>
</template>

<script>
import WaypointRow from '@/vue/components/waypoint-list/waypoint-row'
import Loading from '@/vue/parts/loading'
// import {translate} from '@/vue/services/translation-service'

export default {
    name: 'WaypointList',
    components: {
        WaypointRow,
        Loading,
    },
    props: {
        loading: {
            type: Boolean,
            required: true,
        },
        waypoints: {
            type: Array,
            required: true,
        },
    },
    // methods: {
    //     translate(key) {
    //         return translate(key)
    //     },
    // },
}
</script>
