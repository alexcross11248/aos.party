#include <stdio.h>
#include <stdlib.h>
#include "lodepng.h"
#include "lodepng.c"

int width = 512;//1024;
int height = 512;//576;

unsigned char* file_load(const char* name) {
	FILE *f;
	f = fopen(name,"rb");
	fseek(f,0,SEEK_END);
	int size = ftell(f);
	unsigned char* data = malloc(size);
	fseek(f,0,SEEK_SET);
	fread(data,size,1,f);
	fclose(f);
	return data;
}

unsigned char* image;

void Plot(int x, int y, int color) {
	//x /= 6;
	//y /= 6;
	image[(x+y*width)*4+2] = color & 255;
	image[(x+y*width)*4+1] = (color>>8) & 255;
	image[(x+y*width)*4+0] = (color>>16) & 255;
	image[(x+y*width)*4+3] = 255;
}

void map_vxl_setcolor(int x, int y, int z, int color) {
	z = 63-z;
	x = 511-x;
	int px = -x+y+512;
    int py = (x+y)/2-z+64;
    Plot(px,py,color);
	Plot(px,py-1,color);
	Plot(px,py+1,color);
	Plot(px+1,py,color);
	Plot(px-1,py,color);
}

void map_vxl_load(unsigned char* v) {
   unsigned char* base = v;
   int x,y,z;
   for (y=0; y < 512; ++y) {
      for (x=0; x < 512; ++x) {
         /*for (z=0; z < 64; ++z) {
            map_vxl_setgeom(x,y,z,0x004080,map);
         }*/
         z = 0;
         for(;;) {
            unsigned int *color;
            int i;
            int number_4byte_chunks = v[0];
            int top_color_start = v[1];
            int top_color_end   = v[2]; // inclusive
            int bottom_color_start;
            int bottom_color_end; // exclusive
            int len_top;
            int len_bottom;

            for(i=z; i < top_color_start; i++)
             //  map_vxl_setcolor(x,y,i,0x004080);

            color = (unsigned int *) (v+4);
            for(z=top_color_start; z <= top_color_end; z++)
               map_vxl_setcolor(x,y,z,*color++);

            len_bottom = top_color_end - top_color_start + 1;

            // check for end of data marker
            if (number_4byte_chunks == 0) {
               // infer ACTUAL number of 4-byte chunks from the length of the color data
               v += 4 * (len_bottom + 1);
               break;
            }

            // infer the number of bottom colors in next span from chunk length
            len_top = (number_4byte_chunks-1) - len_bottom;

            // now skip the v pointer past the data to the beginning of the next span
            v += v[0]*4;

            bottom_color_end   = v[3]; // aka air start
            bottom_color_start = bottom_color_end - len_top;

            for(z=bottom_color_start; z < bottom_color_end; ++z) {
               map_vxl_setcolor(x,y,z,*color++);
            }
         }
      }
   }
   v = base;
}

void map_vxl_load2(unsigned char* v) {
   unsigned char* base = v;
   int x,y,z;
   for (y=0; y < 512; ++y) {
      for (x=0; x < 512; ++x) {
         /*for (z=0; z < 64; ++z) {
            map_vxl_setgeom(x,y,z,0x004080,map);
         }*/
         z = 0;
		 int highest_z = 64;
		 int highest_color = 0;
         for(;;) {
            unsigned int *color;
            int i;
            int number_4byte_chunks = v[0];
            int top_color_start = v[1];
            int top_color_end   = v[2]; // inclusive
            int bottom_color_start;
            int bottom_color_end; // exclusive
            int len_top;
            int len_bottom;

            for(i=z; i < top_color_start; i++)
             //  map_vxl_setcolor(x,y,i,0x004080);

            color = (unsigned int *) (v+4);
            for(z=top_color_start; z <= top_color_end; z++) {
				if(z<highest_z) {
					highest_z = z;
					highest_color = *color;
				}
				color++;
			}

            len_bottom = top_color_end - top_color_start + 1;

            // check for end of data marker
            if (number_4byte_chunks == 0) {
               // infer ACTUAL number of 4-byte chunks from the length of the color data
               v += 4 * (len_bottom + 1);
               break;
            }

            // infer the number of bottom colors in next span from chunk length
            len_top = (number_4byte_chunks-1) - len_bottom;

            // now skip the v pointer past the data to the beginning of the next span
            v += v[0]*4;

            bottom_color_end   = v[3]; // aka air start
            bottom_color_start = bottom_color_end - len_top;

            for(z=bottom_color_start; z < bottom_color_end; ++z) {
				if(z<highest_z) {
					highest_z = z;
					highest_color = *color;
				}
				color++;
            }
         }
		 Plot(x,y,highest_color);
      }
   }
   v = base;
}

int main(int len, char** args) {
	image = malloc(width*height*4);
	
	printf("%s\n",args[1]);
	for(int k=0;k<width*height*4;k+=4) {
		image[k+0] = 255;
		image[k+1] = 0;
		image[k+2] = 255;
		image[k+3] = 0;
	}
	
	//map_vxl_load(file_load(args[1]));
	map_vxl_load2(file_load(args[1]));
	
	lodepng_encode32_file(args[2],image,width,height);
	return 0;
}