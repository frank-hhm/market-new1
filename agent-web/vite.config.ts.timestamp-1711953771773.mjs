// vite.config.ts
import { defineConfig } from "file:///E:/wps/wps-system/client-web-v2/node_modules/vite/dist/node/index.js";
import vue from "file:///E:/wps/wps-system/client-web-v2/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import image from "file:///E:/wps/wps-system/client-web-v2/node_modules/rollup-plugin-image/dist/rollup-plugin-image.cjs.js";
import AutoImport from "file:///E:/wps/wps-system/client-web-v2/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///E:/wps/wps-system/client-web-v2/node_modules/unplugin-vue-components/dist/vite.mjs";
import { ArcoResolver } from "file:///E:/wps/wps-system/client-web-v2/node_modules/unplugin-vue-components/dist/resolvers.mjs";
import { vitePluginForArco } from "file:///E:/wps/wps-system/client-web-v2/node_modules/@arco-plugins/vite-vue/lib/index.js";
import path from "path";
var __vite_injected_original_dirname = "E:\\wps\\wps-system\\client-web-v2";
var vite_config_default = defineConfig({
  base: "./",
  plugins: [
    AutoImport({
      resolvers: [ArcoResolver()]
    }),
    Components({
      resolvers: [
        ArcoResolver({
          sideEffect: true
        })
      ]
    }),
    vue(),
    vitePluginForArco({
      style: "css"
    })
  ],
  css: {
    preprocessorOptions: {
      less: {
        modifyVars: {
          "arcoblue-1": "#E8FFEA",
          "arcoblue-2": "#AFF0B5",
          "arcoblue-3": "#7BE188",
          "arcoblue-4": "#4CD263",
          "arcoblue-5": "#23C343",
          "arcoblue-6": "#00B42A",
          "arcoblue-7": "#009A29",
          "arcoblue-8": "#008026",
          "arcoblue-9": "#006622",
          "arcoblue-10": "#004D1C"
        },
        javascriptEnabled: true
      }
    }
  },
  resolve: {
    alias: {
      "@": path.resolve(__vite_injected_original_dirname, "./src")
    }
  },
  server: {
    https: false,
    port: 80,
    host: "localhost"
  },
  build: {
    outDir: path.resolve(__vite_injected_original_dirname, "../server-api/public/client"),
    rollupOptions: {
      plugins: [
        image({
          extensions: /\.(png|jpg|jpeg)$/
        })
      ]
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJFOlxcXFx3cHNcXFxcd3BzLXN5c3RlbVxcXFxjbGllbnQtd2ViLXYyXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJFOlxcXFx3cHNcXFxcd3BzLXN5c3RlbVxcXFxjbGllbnQtd2ViLXYyXFxcXHZpdGUuY29uZmlnLnRzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9FOi93cHMvd3BzLXN5c3RlbS9jbGllbnQtd2ViLXYyL3ZpdGUuY29uZmlnLnRzXCI7Ly8gMy4zdml0ZS5jb25maWcudHNcdTUxNzdcdTRGNTNcdTkxNERcdTdGNkVcbmltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gJ3ZpdGUnXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcbmltcG9ydCBpbWFnZSBmcm9tICdyb2xsdXAtcGx1Z2luLWltYWdlJztcbmltcG9ydCBBdXRvSW1wb3J0IGZyb20gJ3VucGx1Z2luLWF1dG8taW1wb3J0L3ZpdGUnXG5pbXBvcnQgQ29tcG9uZW50cyBmcm9tICd1bnBsdWdpbi12dWUtY29tcG9uZW50cy92aXRlJztcbmltcG9ydCB7IEFyY29SZXNvbHZlciB9IGZyb20gJ3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3Jlc29sdmVycyc7XG5pbXBvcnQgeyB2aXRlUGx1Z2luRm9yQXJjbyB9IGZyb20gJ0BhcmNvLXBsdWdpbnMvdml0ZS12dWUnXG5cblxuaW1wb3J0IHBhdGggZnJvbSAncGF0aCdcblxuY29uc3QgYnVpbGRPcHRpb25zID0ge1xuICBhc3NldHNJbmNsdWRlOiBbJyoqLyouanBnJywgJyoqLyoucG5nJywgJyoqLyouc3ZnJ10gYXMgc3RyaW5nW11cbn07XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gIGJhc2U6ICcuLycsXG4gIC8vIC4uLlxuICBwbHVnaW5zOiBbXG4gICAgLy8gLi4uXG4gICAgQXV0b0ltcG9ydCh7XG4gICAgICByZXNvbHZlcnM6IFtBcmNvUmVzb2x2ZXIoKV0sXG4gICAgfSksXG4gICAgQ29tcG9uZW50cyh7XG4gICAgICByZXNvbHZlcnM6IFtcbiAgICAgICAgQXJjb1Jlc29sdmVyKHtcbiAgICAgICAgICBzaWRlRWZmZWN0OiB0cnVlXG4gICAgICAgIH0pXG4gICAgICBdXG4gICAgfSksXG4gICAgdnVlKCksXG4gICAgdml0ZVBsdWdpbkZvckFyY28oe1xuICAgICAgc3R5bGU6ICdjc3MnXG4gICAgfSlcbiAgXSxcbiAgY3NzOiB7XG4gICAgcHJlcHJvY2Vzc29yT3B0aW9uczoge1xuICAgICAgbGVzczp7XG4gICAgICAgIG1vZGlmeVZhcnM6IHtcbiAgICAgICAgICAnYXJjb2JsdWUtMSc6ICcjRThGRkVBJyxcbiAgICAgICAgICAnYXJjb2JsdWUtMic6ICcjQUZGMEI1JyxcbiAgICAgICAgICAnYXJjb2JsdWUtMyc6ICcjN0JFMTg4JyxcbiAgICAgICAgICAnYXJjb2JsdWUtNCc6ICcjNENEMjYzJyxcbiAgICAgICAgICAnYXJjb2JsdWUtNSc6ICcjMjNDMzQzJyxcbiAgICAgICAgICAnYXJjb2JsdWUtNic6ICcjMDBCNDJBJyxcbiAgICAgICAgICAnYXJjb2JsdWUtNyc6ICcjMDA5QTI5JyxcbiAgICAgICAgICAnYXJjb2JsdWUtOCc6ICcjMDA4MDI2JyxcbiAgICAgICAgICAnYXJjb2JsdWUtOSc6ICcjMDA2NjIyJyxcbiAgICAgICAgICAnYXJjb2JsdWUtMTAnOiAnIzAwNEQxQycsXG4gICAgICAgIH0sXG4gICAgICAgIGphdmFzY3JpcHRFbmFibGVkOiB0cnVlXG4gICAgICB9XG4gICAgfVxuICB9LFxuICByZXNvbHZlOiB7XG4gICAgYWxpYXM6IHtcbiAgICAgICdAJzogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJy4vc3JjJylcbiAgICB9XG4gIH0sXG4gIHNlcnZlcjoge1xuICAgIC8vIFx1NjYyRlx1NTQyNlx1NUYwMFx1NTQyRiBodHRwc1xuICAgIGh0dHBzOiBmYWxzZSxcbiAgICAvLyBcdTdBRUZcdTUzRTNcdTUzRjdcbiAgICBwb3J0OiA4MCxcbiAgICBob3N0OiAnbG9jYWxob3N0JyxcbiAgICAvLyBwcm94eToge1xuICAgIC8vICAgJy9zeXMnOiB7XG4gICAgLy8gICAgIHRhcmdldDogJ2h0dHA6Ly9kZXYtYWRtaW4uZGV2LWZyYW5rLmNuJywgXG4gICAgLy8gICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSxcbiAgICAvLyAgICAgcmV3cml0ZTogKHBhdGgpID0+IHBhdGgucmVwbGFjZSgvXlxcL3N5cy8sICcvc3lzJyksXG4gICAgLy8gICAgIHNlY3VyZTogZmFsc2UsIFxuICAgIC8vICAgfSxcbiAgICAvLyAgICcvaW5kZXgnOiB7XG4gICAgLy8gICAgIHRhcmdldDogJ2h0dHA6Ly9kZXYtYWRtaW4uZGV2LWZyYW5rLmNuJywgXG4gICAgLy8gICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSxcbiAgICAvLyAgICAgcmV3cml0ZTogKHBhdGgpID0+IHBhdGgucmVwbGFjZSgvXlxcL2luZGV4LywgJy9pbmRleCcpLFxuICAgIC8vICAgICBzZWN1cmU6IGZhbHNlLCBcbiAgICAvLyAgIH0sXG4gICAgLy8gfSxcbiAgfSxcbiAgYnVpbGQ6IHtcbiAgICBvdXREaXI6IHBhdGgucmVzb2x2ZShfX2Rpcm5hbWUsICcuLi9zZXJ2ZXItYXBpL3B1YmxpYy9jbGllbnQnKSxcbiAgICByb2xsdXBPcHRpb25zOiB7XG4gICAgICBwbHVnaW5zOiBbXG4gICAgICAgIGltYWdlKHtcbiAgICAgICAgICBleHRlbnNpb25zOiAvXFwuKHBuZ3xqcGd8anBlZykkL1xuICAgICAgICB9KVxuICAgICAgXVxuICAgIH1cbiAgfSxcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQ0EsU0FBUyxvQkFBb0I7QUFDN0IsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sV0FBVztBQUNsQixPQUFPLGdCQUFnQjtBQUN2QixPQUFPLGdCQUFnQjtBQUN2QixTQUFTLG9CQUFvQjtBQUM3QixTQUFTLHlCQUF5QjtBQUdsQyxPQUFPLFVBQVU7QUFWakIsSUFBTSxtQ0FBbUM7QUFnQnpDLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQzFCLE1BQU07QUFBQSxFQUVOLFNBQVM7QUFBQSxJQUVQLFdBQVc7QUFBQSxNQUNULFdBQVcsQ0FBQyxhQUFhLENBQUM7QUFBQSxJQUM1QixDQUFDO0FBQUEsSUFDRCxXQUFXO0FBQUEsTUFDVCxXQUFXO0FBQUEsUUFDVCxhQUFhO0FBQUEsVUFDWCxZQUFZO0FBQUEsUUFDZCxDQUFDO0FBQUEsTUFDSDtBQUFBLElBQ0YsQ0FBQztBQUFBLElBQ0QsSUFBSTtBQUFBLElBQ0osa0JBQWtCO0FBQUEsTUFDaEIsT0FBTztBQUFBLElBQ1QsQ0FBQztBQUFBLEVBQ0g7QUFBQSxFQUNBLEtBQUs7QUFBQSxJQUNILHFCQUFxQjtBQUFBLE1BQ25CLE1BQUs7QUFBQSxRQUNILFlBQVk7QUFBQSxVQUNWLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGVBQWU7QUFBQSxRQUNqQjtBQUFBLFFBQ0EsbUJBQW1CO0FBQUEsTUFDckI7QUFBQSxJQUNGO0FBQUEsRUFDRjtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ1AsT0FBTztBQUFBLE1BQ0wsS0FBSyxLQUFLLFFBQVEsa0NBQVcsT0FBTztBQUFBLElBQ3RDO0FBQUEsRUFDRjtBQUFBLEVBQ0EsUUFBUTtBQUFBLElBRU4sT0FBTztBQUFBLElBRVAsTUFBTTtBQUFBLElBQ04sTUFBTTtBQUFBLEVBZVI7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNMLFFBQVEsS0FBSyxRQUFRLGtDQUFXLDZCQUE2QjtBQUFBLElBQzdELGVBQWU7QUFBQSxNQUNiLFNBQVM7QUFBQSxRQUNQLE1BQU07QUFBQSxVQUNKLFlBQVk7QUFBQSxRQUNkLENBQUM7QUFBQSxNQUNIO0FBQUEsSUFDRjtBQUFBLEVBQ0Y7QUFDRixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
