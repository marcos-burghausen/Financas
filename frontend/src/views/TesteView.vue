<template>
  <div class="box">
    <div class="box__circle">
      <svg>
        <circle
          cx="70"
          cy="70"
          r="20"
        />
      </svg>
      <svg
        height="400"
        width="450"
        xmlns="http://www.w3.org/2000/svg"
      >

        <!-- Draw the paths -->
        <path
          id="lineAB"
          d="M 100 350 l 150 -300"
          stroke="red"
          stroke-width="4"
        />
        <path
          id="lineBC"
          d="M 250 50 l 150 300"
          stroke="red"
          stroke-width="4"
        />
        <path
          id="lineMID"
          d="M 175 200 l 150 0"
          stroke="green"
          stroke-width="4"
        />
        <path
          id="lineAC"
          d="M 100 350 q 150 100 300 0"
          stroke="blue"
          stroke-width="4"
          fill="none"
        />

        <!-- Mark relevant points -->
        <g
          stroke="black"
          stroke-width="3"
          fill="black"
        >
          <circle
            id="pointA"
            cx="100"
            cy="350"
            r="4"
          />
          <circle
            id="pointB"
            cx="250"
            cy="50"
            r="4"
          />
          <circle
            id="pointC"
            cx="400"
            cy="350"
            r="4"
          />
        </g>

        <!-- Label the points -->
        <g
          font-size="30"
          font-family="sans-serif"
          fill="green"
          text-anchor="middle"
        >
          <text
            x="100"
            y="350"
            dx="-30"
          >A</text>
          <text
            x="250"
            y="50"
            dy="-10"
          >B</text>
          <text
            x="400"
            y="350"
            dx="30"
          >C</text>
        </g>

      </svg>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
/* .box {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.box__circle {
  border: 1px solid white;
  height: 250px;
  width: 250px;

}
circle {
  width: 300px;
  height: 300px;
  fill: none;
  stroke: blue;
  stroke-width: 150;
  transform: translate(10px, 10px);
  stroke-dasharray: 125; */
/* } */
</style>