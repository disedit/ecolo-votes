<template>
  <svg version="1.1" x="0px" y="0px"
    width="1369.6px" height="1370.1px" viewBox="0 0 1369.6 1370.1"
    style="overflow:visible;enable-background:new 0 0 1369.6 1370.1;" xml:space="preserve">
  <g>
    <polyline class="st0" points="283.8,16 16,16 16,260.4 	"/>
    <polyline class="st0" points="1353.6,283.8 1353.6,16 1109.1,16 	"/>
    <polyline class="st0" points="1085.8,1354.1 1353.6,1354.1 1353.6,1109.6 	"/>
    <polyline class="st0" points="16,1086.3 16,1354.1 260.4,1354.1 	"/>
  </g>
  <line class="st0 scan-line" x1="72.1" y1="113" x2="1281.2" y2="113"/>
  </svg>
</template>

<style lang="scss" scoped>
  .st0{fill:none;stroke:#FFFFFF;stroke-width:25;stroke-miterlimit:10;}
  .scan-line {
    animation: scan 5s infinite;
    filter: drop-shadow(0 0 20px #fff);
  }

  @keyframes scan {
    0% {
      transform: translateY(0);
    }

    50% {
      transform: translateY(1150px);
    }

    100% {
      transform: translateY(0);
    }
  }
</style>