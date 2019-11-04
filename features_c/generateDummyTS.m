NperN = 100;
Ns = [50000] % logspace(2, 7, 6);

counter = 0;
for N = Ns

    disp(N);
    
    x = linspace(0, 20, N);
    if ~isdir('dummyTS')
        mkdir('dummyTS');
    end
    
    for Nsub = 1:NperN
    
        freq1 = 0.5 + rand * 2;
        freq2 = 0.5 + rand * 10;
        m = rand;
        eta = rand;
        
        y = sin(freq1*pi*x) + cos(freq2 * 2*pi*x) + m*x + rand(1,N)*eta; 

        f = fopen(sprintf('dummyTS/tsid%04i.txt', counter), 'wb');
        fprintf(f, '%2.6f\n', y);
        fclose(f);
        counter = counter + 1;
    end
    
end