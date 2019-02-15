<?php
    class lumpdf{

        protected $buffer = '';
        protected $numPages = 1;
        protected $objectAmount = 4;
        protected $textObjects = array();
        protected $pageHeight = 700;
        protected $pageWidth = 500;

        function generate(){
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename=test.pdf');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            $this->putHeader();
            $this->putCatalog();
            $this->putPages();
            $this->putPage();
            $this->putFont();
            $this->putContent();
            $this->putTrailer();
            $this->putEof();
            echo $this->buffer;
        }

        public function setPageSize($width, $height){
            $this->pageWidth = $width;
            $this->pageHeight = $height;
        }

        public function addContent($text, $x, $y){
            $this->objectAmount++;
            $this->textObjects[] = array("object_id"=>$this->objectAmount,"text"=>$text,"x"=>$x,"y"=>$y);
        }

        private function putHeader(){
            $this->writeToBuffer("%PDF-1.4");
        }

        private function putCatalog(){
            $this->writeToBuffer("1 0 obj");
                $this->writeToBuffer("<</Type /Catalog");
                    $this->writeToBuffer("/Pages 3 0 R");
                $this->writeToBuffer(">>");
            $this->writeToBuffer("endobj");
        }

        private function putPages(){
           $this->writeToBuffer("3 0 obj");
               $this->writeToBuffer("<</Type /Pages");
                   $this->writeToBuffer("/Kids [4 0 R]");
                   $this->writeToBuffer("/Count ".$this->numPages);
               $this->writeToBuffer(">>");
          $this->writeToBuffer("endobj");
        }

        private function putPage(){
            $this->writeToBuffer("4 0 obj");
                $this->writeToBuffer("<</Type /Page");
                    $this->writeToBuffer("/Parent 2 0 R");
                    $this->writeToBuffer("/MediaBox [0 0 ".$this->pageWidth." ".$this->pageHeight."]");

                    $contents = "";
                    $count=0;
                    foreach ($this->textObjects as $value) {
                        if($count===0){
                            $contents .= $value["object_id"]." 0 R";
                        }else{
                            $contents .= " ".$value["object_id"]." 0 R";
                        }
                        print_r($contents);
                        $count++;
                    }

                    $this->writeToBuffer("/Contents [".$contents."]");
                    $this->writeToBuffer("/Resources <</ProcSet [/PDF /Text]");
                        $this->writeToBuffer("/Font <</F1 2 0 R>>");
                    $this->writeToBuffer(">>");
                $this->writeToBuffer(">>");
            $this->writeToBuffer("endobj");
        }

        private function putFont(){
            $this->writeToBuffer("2 0 obj");
                $this->writeToBuffer("<</Type /Font");
                    $this->writeToBuffer("/Subtype /Type1");
                    $this->writeToBuffer("/Name /F1");
                    $this->writeToBuffer("/BaseFont /Helvetica");
                    $this->writeToBuffer("/Encoding /MacRomanEncoding");
                $this->writeToBuffer(">>");
            $this->writeToBuffer("endobj");
        }

        public function putContent(){
            file_put_contents('test', print_r($this->textObjects, true));
            foreach ($this->textObjects as $value) {   
                $this->writeToBuffer($value["object_id"]." 0 obj");
                    $this->writeToBuffer("<</Length 53");
                    $this->writeToBuffer(">>");
                    $this->writeToBuffer("stream");
                        $this->writeToBuffer("BT");
                        $this->writeToBuffer("/F1 20 Tf");
                        $this->writeToBuffer($value["x"]." ".$value["y"]." Td");
                        $this->writeToBuffer("(".$value["text"].") Tj");
                        $this->writeToBuffer("ET");
                    $this->writeToBuffer("endstream");
                $this->writeToBuffer("endobj");
            }
        }

        private function putTrailer(){
            $this->buffer .= "xref\n";
                $this->writeToBuffer("0 ".($this->objectAmount+1));
                $this->writeToBuffer("0000000000 65535 f");
                $this->writeToBuffer("0000000009 00000 n");
                $this->writeToBuffer("0000000063 00000 n");
                $this->writeToBuffer("0000000124 00000 n");
                $this->writeToBuffer("0000000277 00000 n");
                $this->writeToBuffer("0000000392 00000 n");
                $this->writeToBuffer("trailer");
                $this->writeToBuffer("<</Size ".($this->objectAmount+1)."");
                    $this->writeToBuffer("/Root 1 0 R");
                $this->writeToBuffer(">>");
                $this->writeToBuffer("startxref");
            $this->writeToBuffer("502");
        }

        private function putEof(){
            $this->writeToBuffer("%%EOF");
        }

        private function writeToBuffer($text){
            $this->buffer .= $text."\n";
        }
    }
?>