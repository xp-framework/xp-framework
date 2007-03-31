OUTPUT_DIR=./out

SRC= thesis.tex

view: all
	acroread $(OUTPUT_DIR)/thesis.pdf

all: clean $(SRC)
	-rubber -d -I ./lib --into=$(OUTPUT_DIR) thesis.tex

.PHONY: clean
clean:
	cd $(OUTPUT_DIR) && rm -f *

count:
	find ./ -name \*.tex -exec cat {} \; |grep -v % |wc

rtf:
	latex2rtf -o out/synopsis_de.rtf synopsis_de.tex
	latex2rtf -o out/synopsis_en.rtf synopsis_en.tex

iso:
	mkisofs -m .svn -m thesis.iso -J -R -l -o out/thesis.iso out/thesis.pdf out/*.rtf ..

