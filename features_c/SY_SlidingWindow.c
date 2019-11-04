#include <math.h>
#include <string.h>
#include <stdlib.h>
#include "stats.h"

struct window {
    int start;
    int end;
};

typedef struct window win;

static void get_window(win *window, int win_length, int step, int inc)
{
    window->start = (step) * inc;
    window->end = (step) * inc + win_length - 1;
    return;
}

double sy_sliding_window(double y[], int size, int num_seg, int inc_move)
{
    int win_length, inc, num_steps;
    int r = 4;
    win_length = floor(size / num_seg);
    inc = floor(win_length / inc_move);
    inc = (inc == 0) ? 1 : inc;
    num_steps = (int)(floor((size - win_length) / inc) + 1);
    double * qs = malloc(num_steps * sizeof *qs);
    win w;
    for (int i = 0; i < num_steps; i++) {
        get_window(&w, win_length, i, inc);
        qs[i] = moment(y, size, w.start, w.end, r);
    }
    double out = stddev(qs, num_steps) / stddev(y, size);
    free(qs);
    return out;
}