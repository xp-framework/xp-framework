OUTPUT_DIR=./out

SRC= thesis.tex

view: all
	acroread $(OUTPUT_DIR)/thesis.pdf

all: clean $(SRC)
	-rubber -d -I ./lib --into=$(OUTPUT_DIR) thesis.tex

count: 
	find -name \*.tex -exec cat {} \; |wc 

.PHONY: clean
clean:
	cd $(OUTPUT_DIR) && rm -f *

count:
	find ./ -name \*.tex -exec cat {} \; |grep -v % |wc

