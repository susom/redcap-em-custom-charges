<template>
    <div id="app" class="container-fluid">
        <div class="row">
            <div class="col-12">
                <ChargesComponent/>
            </div>
        </div>

    </div>

</template>

<script>
import ChargesComponent from "@/components/ChargesComponent";

export default {
    name: 'App',
    components: {
        ChargesComponent,
    }
}
</script>

<style>
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
}
</style>
