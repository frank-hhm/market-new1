// vite.config.ts
import { defineConfig } from "file:///E:/pro/pro-market-3new/admin-web/node_modules/vite/dist/node/index.js";
import vue from "file:///E:/pro/pro-market-3new/admin-web/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import image from "file:///E:/pro/pro-market-3new/admin-web/node_modules/rollup-plugin-image/dist/rollup-plugin-image.cjs.js";
import AutoImport from "file:///E:/pro/pro-market-3new/admin-web/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///E:/pro/pro-market-3new/admin-web/node_modules/unplugin-vue-components/dist/vite.mjs";
import { ArcoResolver } from "file:///E:/pro/pro-market-3new/admin-web/node_modules/unplugin-vue-components/dist/resolvers.mjs";
import { vitePluginForArco } from "file:///E:/pro/pro-market-3new/admin-web/node_modules/@arco-plugins/vite-vue/lib/index.js";
import path from "path";
var __vite_injected_original_dirname = "E:\\pro\\pro-market-3new\\admin-web";
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
    outDir: path.resolve(__vite_injected_original_dirname, "../server-api/public/admin"),
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcudHMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJFOlxcXFxwcm9cXFxccHJvLW1hcmtldC0zbmV3XFxcXGFkbWluLXdlYlwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiRTpcXFxccHJvXFxcXHByby1tYXJrZXQtM25ld1xcXFxhZG1pbi13ZWJcXFxcdml0ZS5jb25maWcudHNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0U6L3Byby9wcm8tbWFya2V0LTNuZXcvYWRtaW4td2ViL3ZpdGUuY29uZmlnLnRzXCI7Ly8gMy4zdml0ZS5jb25maWcudHNcdTUxNzdcdTRGNTNcdTkxNERcdTdGNkVcclxuaW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSdcclxuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnXHJcbmltcG9ydCBpbWFnZSBmcm9tICdyb2xsdXAtcGx1Z2luLWltYWdlJztcclxuaW1wb3J0IEF1dG9JbXBvcnQgZnJvbSAndW5wbHVnaW4tYXV0by1pbXBvcnQvdml0ZSdcclxuaW1wb3J0IENvbXBvbmVudHMgZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZSc7XHJcbmltcG9ydCB7IEFyY29SZXNvbHZlciB9IGZyb20gJ3VucGx1Z2luLXZ1ZS1jb21wb25lbnRzL3Jlc29sdmVycyc7XHJcbmltcG9ydCB7IHZpdGVQbHVnaW5Gb3JBcmNvIH0gZnJvbSAnQGFyY28tcGx1Z2lucy92aXRlLXZ1ZSdcclxuXHJcblxyXG5pbXBvcnQgcGF0aCBmcm9tICdwYXRoJ1xyXG5cclxuY29uc3QgYnVpbGRPcHRpb25zID0ge1xyXG4gIGFzc2V0c0luY2x1ZGU6IFsnKiovKi5qcGcnLCAnKiovKi5wbmcnLCAnKiovKi5zdmcnXSBhcyBzdHJpbmdbXVxyXG59O1xyXG5cclxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcclxuICBiYXNlOiAnLi8nLFxyXG4gIC8vIC4uLlxyXG4gIHBsdWdpbnM6IFtcclxuICAgIC8vIC4uLlxyXG4gICAgQXV0b0ltcG9ydCh7XHJcbiAgICAgIHJlc29sdmVyczogW0FyY29SZXNvbHZlcigpXSxcclxuICAgIH0pLFxyXG4gICAgQ29tcG9uZW50cyh7XHJcbiAgICAgIHJlc29sdmVyczogW1xyXG4gICAgICAgIEFyY29SZXNvbHZlcih7XHJcbiAgICAgICAgICBzaWRlRWZmZWN0OiB0cnVlXHJcbiAgICAgICAgfSlcclxuICAgICAgXVxyXG4gICAgfSksXHJcbiAgICB2dWUoKSxcclxuICAgIHZpdGVQbHVnaW5Gb3JBcmNvKHtcclxuICAgICAgc3R5bGU6ICdjc3MnXHJcbiAgICB9KVxyXG4gIF0sXHJcbiAgY3NzOiB7XHJcbiAgICBwcmVwcm9jZXNzb3JPcHRpb25zOiB7XHJcbiAgICAgIGxlc3M6e1xyXG4gICAgICAgIG1vZGlmeVZhcnM6IHtcclxuICAgICAgICAgICdhcmNvYmx1ZS0xJzogJyNFOEZGRUEnLFxyXG4gICAgICAgICAgJ2FyY29ibHVlLTInOiAnI0FGRjBCNScsXHJcbiAgICAgICAgICAnYXJjb2JsdWUtMyc6ICcjN0JFMTg4JyxcclxuICAgICAgICAgICdhcmNvYmx1ZS00JzogJyM0Q0QyNjMnLFxyXG4gICAgICAgICAgJ2FyY29ibHVlLTUnOiAnIzIzQzM0MycsXHJcbiAgICAgICAgICAnYXJjb2JsdWUtNic6ICcjMDBCNDJBJyxcclxuICAgICAgICAgICdhcmNvYmx1ZS03JzogJyMwMDlBMjknLFxyXG4gICAgICAgICAgJ2FyY29ibHVlLTgnOiAnIzAwODAyNicsXHJcbiAgICAgICAgICAnYXJjb2JsdWUtOSc6ICcjMDA2NjIyJyxcclxuICAgICAgICAgICdhcmNvYmx1ZS0xMCc6ICcjMDA0RDFDJyxcclxuICAgICAgICB9LFxyXG4gICAgICAgIGphdmFzY3JpcHRFbmFibGVkOiB0cnVlXHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9LFxyXG4gIHJlc29sdmU6IHtcclxuICAgIGFsaWFzOiB7XHJcbiAgICAgICdAJzogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJy4vc3JjJylcclxuICAgIH1cclxuICB9LFxyXG4gIHNlcnZlcjoge1xyXG4gICAgLy8gXHU2NjJGXHU1NDI2XHU1RjAwXHU1NDJGIGh0dHBzXHJcbiAgICBodHRwczogZmFsc2UsXHJcbiAgICAvLyBcdTdBRUZcdTUzRTNcdTUzRjdcclxuICAgIHBvcnQ6IDgwLFxyXG4gICAgaG9zdDogJ2xvY2FsaG9zdCcsXHJcbiAgICAvLyBwcm94eToge1xyXG4gICAgLy8gICAnL3N5cyc6IHtcclxuICAgIC8vICAgICB0YXJnZXQ6ICdodHRwOi8vZGV2LWFkbWluLmRldi1mcmFuay5jbicsIFxyXG4gICAgLy8gICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSxcclxuICAgIC8vICAgICByZXdyaXRlOiAocGF0aCkgPT4gcGF0aC5yZXBsYWNlKC9eXFwvc3lzLywgJy9zeXMnKSxcclxuICAgIC8vICAgICBzZWN1cmU6IGZhbHNlLCBcclxuICAgIC8vICAgfSxcclxuICAgIC8vICAgJy9pbmRleCc6IHtcclxuICAgIC8vICAgICB0YXJnZXQ6ICdodHRwOi8vZGV2LWFkbWluLmRldi1mcmFuay5jbicsIFxyXG4gICAgLy8gICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSxcclxuICAgIC8vICAgICByZXdyaXRlOiAocGF0aCkgPT4gcGF0aC5yZXBsYWNlKC9eXFwvaW5kZXgvLCAnL2luZGV4JyksXHJcbiAgICAvLyAgICAgc2VjdXJlOiBmYWxzZSwgXHJcbiAgICAvLyAgIH0sXHJcbiAgICAvLyB9LFxyXG4gIH0sXHJcbiAgYnVpbGQ6IHtcclxuICAgIG91dERpcjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJy4uL3NlcnZlci1hcGkvcHVibGljL2FkbWluJyksXHJcbiAgICByb2xsdXBPcHRpb25zOiB7XHJcbiAgICAgIHBsdWdpbnM6IFtcclxuICAgICAgICBpbWFnZSh7XHJcbiAgICAgICAgICBleHRlbnNpb25zOiAvXFwuKHBuZ3xqcGd8anBlZykkL1xyXG4gICAgICAgIH0pXHJcbiAgICAgIF1cclxuICAgIH1cclxuICB9LFxyXG59KVxyXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQ0EsU0FBUyxvQkFBb0I7QUFDN0IsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sV0FBVztBQUNsQixPQUFPLGdCQUFnQjtBQUN2QixPQUFPLGdCQUFnQjtBQUN2QixTQUFTLG9CQUFvQjtBQUM3QixTQUFTLHlCQUF5QjtBQUdsQyxPQUFPLFVBQVU7QUFWakIsSUFBTSxtQ0FBbUM7QUFnQnpDLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQzFCLE1BQU07QUFBQSxFQUVOLFNBQVM7QUFBQSxJQUVQLFdBQVc7QUFBQSxNQUNULFdBQVcsQ0FBQyxhQUFhLENBQUM7QUFBQSxJQUM1QixDQUFDO0FBQUEsSUFDRCxXQUFXO0FBQUEsTUFDVCxXQUFXO0FBQUEsUUFDVCxhQUFhO0FBQUEsVUFDWCxZQUFZO0FBQUEsUUFDZCxDQUFDO0FBQUEsTUFDSDtBQUFBLElBQ0YsQ0FBQztBQUFBLElBQ0QsSUFBSTtBQUFBLElBQ0osa0JBQWtCO0FBQUEsTUFDaEIsT0FBTztBQUFBLElBQ1QsQ0FBQztBQUFBLEVBQ0g7QUFBQSxFQUNBLEtBQUs7QUFBQSxJQUNILHFCQUFxQjtBQUFBLE1BQ25CLE1BQUs7QUFBQSxRQUNILFlBQVk7QUFBQSxVQUNWLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGNBQWM7QUFBQSxVQUNkLGVBQWU7QUFBQSxRQUNqQjtBQUFBLFFBQ0EsbUJBQW1CO0FBQUEsTUFDckI7QUFBQSxJQUNGO0FBQUEsRUFDRjtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ1AsT0FBTztBQUFBLE1BQ0wsS0FBSyxLQUFLLFFBQVEsa0NBQVcsT0FBTztBQUFBLElBQ3RDO0FBQUEsRUFDRjtBQUFBLEVBQ0EsUUFBUTtBQUFBLElBRU4sT0FBTztBQUFBLElBRVAsTUFBTTtBQUFBLElBQ04sTUFBTTtBQUFBLEVBZVI7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNMLFFBQVEsS0FBSyxRQUFRLGtDQUFXLDRCQUE0QjtBQUFBLElBQzVELGVBQWU7QUFBQSxNQUNiLFNBQVM7QUFBQSxRQUNQLE1BQU07QUFBQSxVQUNKLFlBQVk7QUFBQSxRQUNkLENBQUM7QUFBQSxNQUNIO0FBQUEsSUFDRjtBQUFBLEVBQ0Y7QUFDRixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
